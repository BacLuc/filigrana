package test.jlibav;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import org.libav.DefaultMediaReader;
import org.libav.IDecoder;
import org.libav.IMediaReader;
import org.libav.LibavException;
import org.libav.avcodec.ICodecContextWrapper;
import org.libav.avformat.IStreamWrapper;
import org.libav.video.VideoFrameDecoder;

import test.VideoPlayerFrame;

public class VideoFrameReaderTest {

	public static void main(String[] args) throws LibavException, IOException, InterruptedException {

		int videoStreamIndex = 0;
		IMediaReader mr = new DefaultMediaReader("G:/Open Short Video Collection/collection/Sintel/Sintel.mp4");
		IDecoder decoder = new VideoFrameDecoder(mr.getVideoStream(videoStreamIndex));
        
        mr.addVideoPacketConsumer(videoStreamIndex, decoder);
        mr.setVideoStreamBufferingEnabled(videoStreamIndex, true);
        
        ICodecContextWrapper codecContext = decoder.getCodecContext();
        
        IStreamWrapper stream = decoder.getStream();
        
        System.out.println(getFPS(stream));
        
        // Get some video stream properties.
        int width = codecContext.getWidth();
        int height = codecContext.getHeight();
        
        // Create video frame and show it on screen.
        VideoPlayerFrame videoFrame = new VideoPlayerFrame("Simple Video Player", width, height);
        videoFrame.setVisible(true);
        
        // Get video pane. (It's the component responsible for video rendering.)
        TestVideoPane videoPane = videoFrame.getVideoPane();
        
        // Set source image format.
        videoPane.setSourceImageFormat(width, height, codecContext.getPixelFormat());
        
        // Link the decoder with the video frame.
        decoder.addFrameConsumer(videoFrame.getVideoPane());
		
        
        BufferedReader keyboard = new BufferedReader(new InputStreamReader(System.in));
        
        boolean stop = false;
        
        while (!stop) {
        	//keyboard.readLine();
            try {
                if (!mr.readNextPacket(videoStreamIndex))
                    stop = true;
            } catch (LibavException ex) {
                ex.printStackTrace();
            }
        }
        
        mr.close();
		decoder.close();
	}
	
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
