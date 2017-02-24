package cineast.core.decode.video;

import java.awt.Point;
import java.awt.image.BufferedImage;
import java.awt.image.ColorModel;
import java.awt.image.DataBuffer;
import java.awt.image.DataBufferInt;
import java.awt.image.DirectColorModel;
import java.awt.image.Raster;
import java.awt.image.SampleModel;
import java.awt.image.SinglePixelPackedSampleModel;
import java.awt.image.WritableRaster;
import java.io.File;
import java.nio.ByteOrder;
import java.util.ArrayDeque;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.bridj.Pointer;
import org.libav.DefaultMediaReader;
import org.libav.LibavException;
import org.libav.avcodec.FrameWrapperFactory;
import org.libav.avcodec.ICodecContextWrapper;
import org.libav.avcodec.IFrameWrapper;
import org.libav.avformat.IStreamWrapper;
import org.libav.avutil.bridge.PixelFormat;
import org.libav.data.IFrameConsumer;
import org.libav.swscale.ScaleContextWrapper;
import org.libav.swscale.bridge.SWScaleLibrary;
import org.libav.video.VideoFrameDecoder;

import cineast.core.data.Frame;
import cineast.core.data.MultiImageFactory;
import cineast.core.util.LogHelper;

public class JLibAVVideoDecoder implements VideoDecoder {

	private static final Logger LOGGER = LogManager.getLogger();
	
	private int videoStreamIndex = 0;
	private DefaultMediaReader mediaReader;
	private VideoFrameDecoder decoder;
	private JLibAVFrameConsumer frameConsumer;
	
	private int width, height, framecount;
	
	private float fps;
	
	private boolean hasMorePackets = true;
	
	public JLibAVVideoDecoder(File file){
		if(!file.exists()){
			LOGGER.error("File does not exist {}", file.getAbsoluteFile());
			return;
		}
		
		try {
			this.mediaReader = new DefaultMediaReader(file.getAbsolutePath());
			this.decoder = new VideoFrameDecoder(this.mediaReader.getVideoStream(videoStreamIndex));
		} catch (LibavException e) {
			LOGGER.error("Error while initialising JLibAVVideoDecoder: {}", LogHelper.getStackTrace(e));
		}
		
		this.mediaReader.addVideoPacketConsumer(videoStreamIndex, this.decoder);
		this.mediaReader.setVideoStreamBufferingEnabled(videoStreamIndex, true);
		
		 ICodecContextWrapper codecContext = decoder.getCodecContext();
		 this.width = codecContext.getWidth();
	     this.height = codecContext.getHeight();
	     
	     this.frameConsumer = new JLibAVFrameConsumer(this.width, this.height, codecContext.getPixelFormat());
	     this.decoder.addFrameConsumer(this.frameConsumer);
	     
	     IStreamWrapper stream = decoder.getStream();
	     this.fps = getFPS(stream);
	     this.framecount = (int) stream.getFrameCount();
	}
	
	@Override
	public void seekToFrame(int frameNumber) {
		this.frameConsumer.setSeek(true);
		while(frameNumber > frameConsumer.getFrameNumber()){
			getFrame();
		}
		this.frameConsumer.setSeek(false);
	}

	@Override
	public int getFrameNumber() {
		return this.frameConsumer.getFrameNumber();
	}

	@Override
	public Frame getFrame() {
		Frame _return = this.frameConsumer.getNextFrame();
		if(_return != null){
			return _return;
		}
		while(this.hasMorePackets && (_return = this.frameConsumer.getNextFrame()) == null){
			try {
				this.hasMorePackets = this.mediaReader.readNextPacket(this.videoStreamIndex);
			} catch (LibavException e) {
				this.hasMorePackets = false;
				LOGGER.error("Error while decoding video: {}", LogHelper.getStackTrace(e));
			}
		}
		return _return;
	}

	@Override
	public int getTotalFrameCount() {
		return this.framecount;
	}

	@Override
	public double getFPS() {
		return this.fps;
	}

	@Override
	public void close() {
		this.decoder.close();
		try {
			this.mediaReader.close();
		} catch (LibavException e) {
			LOGGER.warn("Error while closing mediaReader: {}", LogHelper.getStackTrace(e));
		}
		this.frameConsumer.close();
	}

	@Override
	public int getWidth() {
		return this.width;
	}

	@Override
	public int getHeight() {
		return this.height;
	}

	
	 /**
     * http://libav-users.943685.n4.nabble.com/Retrieving-Frames-Per-Second-FPS-td946533.html
     * @param stream
     * @return
     */
    private static float getFPS(IStreamWrapper stream){
    	if(
    			(stream.getTimeBase().getDenominator() != stream.getRFrameRate().getNumerator())
    			||
    			(stream.getTimeBase().getNumerator() != stream.getRFrameRate().getDenominator())
    		){
    		
    		return (float) stream.getRFrameRate().getNumerator() / (float) stream.getRFrameRate().getDenominator();
    		
    	}else{
    		return (float) stream.getTimeBase().getNumerator() / (float) stream.getTimeBase().getDenominator();
    	}
    }
}

class JLibAVFrameConsumer implements IFrameConsumer{

	private boolean seek = false;
	private int frameNumber = 0;
	private int width, height, pixelFormat;
	private ArrayDeque<Frame> frameQueue;
	
	 private ScaleContextWrapper scaleContext;
    private IFrameWrapper rgbFrame;
    private Pointer<Byte> rgbFrameData;
    private BufferedImage img;
    private int[] imageData;
	
	JLibAVFrameConsumer(int width, int height, int pixelFormat) {
		this.width = width;
		this.height = height;
		this.pixelFormat = pixelFormat;
		this.frameQueue = new ArrayDeque<>();
		
		int dstPixelFormat = PixelFormat.PIX_FMT_BGRA;
        if (ByteOrder.BIG_ENDIAN.equals(ByteOrder.nativeOrder())){
        	dstPixelFormat = PixelFormat.PIX_FMT_ARGB;
        }
		
        try{
			 scaleContext = ScaleContextWrapper.createContext(width, height, PixelFormat.PIX_FMT_YUV420P, width, height, dstPixelFormat, SWScaleLibrary.SWS_BICUBIC);
			 rgbFrame = FrameWrapperFactory.getInstance().allocPicture(dstPixelFormat, width, height);
			 rgbFrameData = rgbFrame.getData().get();
        } catch (LibavException ex) {
	    	  
        }
		 
		 imageData = new int[width * height];
		 DataBuffer db = new DataBufferInt(imageData, imageData.length);
		 int[] masks = new int[] { 0x00ff0000, 0x0000ff00, 0x000000ff };
		 SampleModel sm = new SinglePixelPackedSampleModel(DataBuffer.TYPE_INT, width, height, masks);
		 WritableRaster wr = Raster.createWritableRaster(sm, db, new Point());
		 img = new BufferedImage(new DirectColorModel(24, 0x00ff0000, 0x0000ff00, 0x000000ff), wr, false, null);
	}
	
	int getFrameNumber(){
		return this.frameNumber;
	}
	
	void setSeek(boolean seek){
		this.seek = seek;
	}
	
	@Override
	public void processFrame(Object producer, IFrameWrapper frame) throws LibavException {
		if(seek){
			++frameNumber;
			return;
		}
		
		scaleContext.scale(frame, rgbFrame, 0, height);
        
        rgbFrameData.getIntsAtOffset(0, imageData, 0, imageData.length);
        
        this.frameQueue.add(new Frame(++this.frameNumber, MultiImageFactory.newMultiImage(copyImg())));
		
	}
	
	private BufferedImage copyImg() {
		ColorModel cm = img.getColorModel();
		boolean isAlphaPremultiplied = cm.isAlphaPremultiplied();
		WritableRaster raster = img.copyData(null);
		return new BufferedImage(cm, raster, isAlphaPremultiplied, null);
	}
	
	Frame getNextFrame(){
		if(this.frameQueue.isEmpty()){
			return null;
		}
		return this.frameQueue.pop();
	}
	
	void close(){
		if (scaleContext != null)
            scaleContext.free();
        if (rgbFrame != null)
            rgbFrame.free();
        
        scaleContext = null;
	}
	
}