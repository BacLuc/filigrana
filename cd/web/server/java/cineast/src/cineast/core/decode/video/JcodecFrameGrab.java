package cineast.core.decode.video;

import java.io.IOException;
import java.nio.ByteBuffer;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.jcodec.api.FrameGrab.MediaInfo;
import org.jcodec.api.JCodecException;
import org.jcodec.api.UnsupportedFormatException;
import org.jcodec.api.specific.AVCMP4Adaptor;
import org.jcodec.api.specific.ContainerAdaptor;
import org.jcodec.codecs.h264.H264Decoder;
import org.jcodec.codecs.mpeg12.MPEGDecoder;
import org.jcodec.codecs.prores.ProresDecoder;
import org.jcodec.common.DemuxerTrack;
import org.jcodec.common.JCodecUtil;
import org.jcodec.common.JCodecUtil.Format;
import org.jcodec.common.SeekableByteChannel;
import org.jcodec.common.SeekableDemuxerTrack;
import org.jcodec.common.VideoDecoder;
import org.jcodec.common.model.Packet;
import org.jcodec.common.model.Picture;
import org.jcodec.containers.mp4.MP4Packet;
import org.jcodec.containers.mp4.boxes.SampleEntry;
import org.jcodec.containers.mp4.demuxer.AbstractMP4DemuxerTrack;
import org.jcodec.containers.mp4.demuxer.MP4Demuxer;

import cineast.core.util.LogHelper;

/**
 * This class is copied from JCodec ( www.jcodec.org ) and modified.
 * 
 * @author The JCodec project, Luca Rossetto
 * 
 */
public class JcodecFrameGrab {

	private static final Logger LOGGER = LogManager.getLogger();
	
    private DemuxerTrack videoTrack;
    private ContainerAdaptor decoder;
    private ThreadLocal<int[][]> buffers = new ThreadLocal<int[][]>();
    

    public JcodecFrameGrab(SeekableByteChannel in) throws IOException, JCodecException {
        ByteBuffer header = ByteBuffer.allocate(65536);
        in.read(header);
        header.flip();
        Format detectFormat = JCodecUtil.detectFormat(header);

        switch (detectFormat) {
        case MOV:
            MP4Demuxer d1 = new MP4Demuxer(in);
            videoTrack = d1.getVideoTrack();
            break;
        case MPEG_PS:
            throw new UnsupportedFormatException("MPEG PS is temporarily unsupported.");
        case MPEG_TS:
            throw new UnsupportedFormatException("MPEG TS is temporarily unsupported.");
        default:
            throw new UnsupportedFormatException("Container format is not supported by JCodec");
        }
        decodeLeadingFrames();
    }

    public JcodecFrameGrab(SeekableDemuxerTrack videoTrack, ContainerAdaptor decoder) {
        this.videoTrack = videoTrack;
        this.decoder = decoder;
    }

    private SeekableDemuxerTrack sdt() throws JCodecException {
        if (!(videoTrack instanceof SeekableDemuxerTrack)) {
			throw new JCodecException("Not a seekable track");
		}

        return (SeekableDemuxerTrack) videoTrack;
    }

    /**
     * Position frame grabber to a specific second in a movie. As a result the
     * next decoded frame will be precisely at the requested second.
     * 
     * WARNING: potentially very slow. Use only when you absolutely need precise
     * seek. Tries to seek to exactly the requested second and for this it might
     * have to decode a sequence of frames from the closes key frame. Depending
     * on GOP structure this may be as many as 500 frames.
     * 
     * @param second
     * @return
     * @throws IOException
     * @throws JCodecException
     */
    public JcodecFrameGrab seekToSecondPrecise(double second) throws IOException, JCodecException {
        sdt().seek(second);
        decodeLeadingFrames();
        return this;
    }

    /**
     * Position frame grabber to a specific frame in a movie. As a result the
     * next decoded frame will be precisely the requested frame number.
     * 
     * WARNING: potentially very slow. Use only when you absolutely need precise
     * seek. Tries to seek to exactly the requested frame and for this it might
     * have to decode a sequence of frames from the closes key frame. Depending
     * on GOP structure this may be as many as 500 frames.
     * 
     * @param frameNumber
     * @return
     * @throws IOException
     * @throws JCodecException
     */
    public JcodecFrameGrab seekToFramePrecise(int frameNumber) throws IOException, JCodecException {
        sdt().gotoFrame(frameNumber);
        decodeLeadingFrames();
        return this;
    }

    /**
     * Position frame grabber to a specific second in a movie.
     * 
     * Performs a sloppy seek, meaning that it may actually not seek to exact
     * second requested, instead it will seek to the closest key frame
     * 
     * NOTE: fast, as it just seeks to the closest previous key frame and
     * doesn't try to decode frames in the middle
     * 
     * @param second
     * @return
     * @throws IOException
     * @throws JCodecException
     */
    public JcodecFrameGrab seekToSecondSloppy(double second) throws IOException, JCodecException {
        sdt().seek(second);
        goToPrevKeyframe();
        return this;
    }

    /**
     * Position frame grabber to a specific frame in a movie
     * 
     * Performs a sloppy seek, meaning that it may actually not seek to exact
     * frame requested, instead it will seek to the closest key frame
     * 
     * NOTE: fast, as it just seeks to the closest previous key frame and
     * doesn't try to decode frames in the middle
     * 
     * @param frameNumber
     * @return
     * @throws IOException
     * @throws JCodecException
     */
    public JcodecFrameGrab seekToFrameSloppy(int frameNumber) throws IOException, JCodecException {
        sdt().gotoFrame(frameNumber);
        goToPrevKeyframe();
        return this;
    }

    private void goToPrevKeyframe() throws IOException, JCodecException {
        sdt().gotoFrame(detectKeyFrame((int) sdt().getCurFrame()));
    }

    private void decodeLeadingFrames() throws IOException, JCodecException {
        SeekableDemuxerTrack sdt = sdt();

        int curFrame = (int) sdt.getCurFrame();
        int keyFrame = detectKeyFrame(curFrame);
        sdt.gotoFrame(keyFrame);

        Packet frame = sdt.nextFrame();
        decoder = detectDecoder(sdt, frame);

        while (frame.getFrameNo() < curFrame) {
            decoder.decodeFrame(frame, getBuffer());
            frame = sdt.nextFrame();
        }
        sdt.gotoFrame(curFrame);
    }

    private int[][] getBuffer() {
        int[][] buf = buffers.get();
        if (buf == null) {
            buf = decoder.allocatePicture();
            buffers.set(buf);
        }
        return buf;
    }

    private int detectKeyFrame(int start) throws IOException {
        int[] seekFrames = videoTrack.getMeta().getSeekFrames();
        if (seekFrames == null) {
			return start;
		}
        int prev = seekFrames[0];
        for (int i = 1; i < seekFrames.length; i++) {
            if (seekFrames[i] > start) {
				break;
			}
            prev = seekFrames[i];
        }
        return prev;
    }

    private ContainerAdaptor detectDecoder(SeekableDemuxerTrack videoTrack, Packet frame) throws JCodecException {
        if (videoTrack instanceof AbstractMP4DemuxerTrack) {
            SampleEntry se = ((AbstractMP4DemuxerTrack) videoTrack).getSampleEntries()[((MP4Packet) frame).getEntryNo()];
            VideoDecoder byFourcc = byFourcc(se.getHeader().getFourcc());
            if (byFourcc instanceof H264Decoder) {
				return new AVCMP4Adaptor(((AbstractMP4DemuxerTrack) videoTrack).getSampleEntries());
			}
        }

        throw new UnsupportedFormatException("Codec is not supported");
    }

    private VideoDecoder byFourcc(String fourcc) {
        if (fourcc.equals("avc1")) {
            return new H264Decoder();
        } else if (fourcc.equals("m1v1") || fourcc.equals("m2v1")) {
            return new MPEGDecoder();
        } else if (fourcc.equals("apco") || fourcc.equals("apcs") || fourcc.equals("apcn") || fourcc.equals("apch")
                || fourcc.equals("ap4h")) {
            return new ProresDecoder();
        }
        return null;
    }

    

    /**
     * Get frame at current position in JCodec native image
     * 
     * @return
     * @throws IOException
     */
    public Picture getNativeFrame() throws IOException {
        Packet frame = videoTrack.nextFrame();
        if (frame == null) {
			return null;
		}
        
        return decoder.decodeFrame(frame, getBuffer());
    }
    
    
    /**
     * Gets info about the media
     * @return
     */
    public MediaInfo getMediaInfo() {
    	return decoder.getMediaInfo();
    }
    
    public int getTotalFrames(){
    	return this.videoTrack.getMeta().getTotalFrames();
    }
    
    public double getTotalDuration(){
    	return this.videoTrack.getMeta().getTotalDuration();
    }
    
    public double getFPS(){
    	return getTotalFrames() / getTotalDuration();
    }
    
    public long getCurrentFrameNum(){
    	try {
			return sdt().getCurFrame();
		} catch (JCodecException e) {
			LOGGER.warn(LogHelper.getStackTrace(e));
		}
    	return -1;
    }
}