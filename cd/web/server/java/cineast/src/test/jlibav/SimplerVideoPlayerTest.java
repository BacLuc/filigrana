package test.jlibav;

/*
 * Copyright (C) 2012 Ondrej Perutka
 *
 * This program is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public 
 * License as published by the Free Software Foundation, either 
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with this library. If not, see 
 * <http://www.gnu.org/licenses/>.
 */

import java.util.logging.Level;
import java.util.logging.Logger;

import org.libav.DefaultMediaReader;
import org.libav.IDecoder;
import org.libav.LibavException;
import org.libav.audio.PlaybackMixer;
import org.libav.avcodec.ICodecContextWrapper;
import org.libav.avformat.IStreamWrapper;

import test.VideoPlayerFrame;

/**
 * Enother usage example. It shows how to play some video. It uses features
 * shown in the previous example.
 * 
 * @author Ondrej Perutka
 */
public class SimplerVideoPlayerTest {
    
    private TestMediaPlayer player;
    private PlaybackMixer mixer;
    private VideoPlayerFrame videoFrame;
    
    public SimplerVideoPlayerTest(String url) throws LibavException {
        // Open the given file/stream.
        player = new TestMediaPlayer(new DefaultMediaReader(url), 0);
    }
    
    /**
     * Start video playback.
     * 
     * @throws Exception if something goes wrong
     */
    public void play() throws Exception {
        
       
        openVideoStream(0);
        
        // Start playback and wait until it stops.
        player.play();
        
        
    }
    
    /**
     * Release all resources.
     * 
     * @throws Exception if something goes wrong
     */
    public void close() throws Exception {
        player.close();
        
        if (mixer != null)
           // PlaybackMixer.closeMixer(mixer);
        if (videoFrame != null)
            videoFrame.dispose();
        
        mixer = null;
    }
    
    /**
     * Prepare video stream for playback.
     * 
     * @param videoStreamIndex video stream index
     * @throws LibavException if something goes wrong
     */
    private void openVideoStream(int videoStreamIndex) throws LibavException {
        // Get decoder of the given video stream and its codec context.
        IDecoder decoder = player.getVideoStreamDecoder(videoStreamIndex);
        ICodecContextWrapper codecContext = decoder.getCodecContext();
        
        
        IStreamWrapper stream = decoder.getStream();
        
        System.out.println("FrameCount: " + stream.getFrameCount());
        System.out.println("Duration: " + stream.getDuration());
        
        System.out.printf("STREAM TIME BASE - FPS : %d / %d \n", stream.getTimeBase().getNumerator(), stream.getTimeBase().getDenominator());
        System.out.printf("STREAM R_FRAMERATE - FPS : %d / %d \n", stream.getRFrameRate().getNumerator(), stream.getRFrameRate().getDenominator());
        System.out.printf("CODEC - FPS : %d / %d \n", stream.getCodecContext().getTimeBase().getNumerator(), stream.getCodecContext().getTimeBase().getDenominator());
        
        System.out.println(getFPS(stream));
       
        // Get some video stream properties.
        int width = codecContext.getWidth();
        int height = codecContext.getHeight();
        
        // Create video frame and show it on screen.
        videoFrame = new VideoPlayerFrame("Simple Video Player", width, height);
        videoFrame.setVisible(true);
        
        // Get video pane. (It's the component responsible for video rendering.)
        TestVideoPane videoPane = videoFrame.getVideoPane();
        
        // Set source image format.
        videoPane.setSourceImageFormat(width, height, codecContext.getPixelFormat());
        
        // Link the decoder with the video frame.
        decoder.addFrameConsumer(videoFrame.getVideoPane());
        
       
    }

    
    public static void main(String[] args) {
        SimplerVideoPlayerTest simpleVideoPlayer = null;
        
        try {
            // Get a video file name or stream URL from command line args.
            String url = "G:/Open Short Video Collection/collection/Aqua/Aqua.mp4";
            // Create a new instance of this simple video player.
            simpleVideoPlayer = new SimplerVideoPlayerTest(url);
            // Start playback.
            simpleVideoPlayer.play();
        } catch (Exception ex) {
            Logger.getLogger(SimplerVideoPlayerTest.class.getName()).log(Level.SEVERE, "something's wrong", ex);
        } finally {
            try {
                // We should always release kept resources if we don't need them.
                if (simpleVideoPlayer != null)
                    simpleVideoPlayer.close();
            } catch (Exception ex) {
                Logger.getLogger(SimplerVideoPlayerTest.class.getName()).log(Level.SEVERE, "unable to release system resources", ex);
            }
        }
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
