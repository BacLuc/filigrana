package test;

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

import java.nio.ByteOrder;
import java.util.logging.Level;
import java.util.logging.Logger;

import javax.sound.sampled.AudioFormat;
import javax.sound.sampled.AudioInputStream;

import org.libav.DefaultMediaPlayer;
import org.libav.IDecoder;
import org.libav.IMediaPlayer;
import org.libav.IMediaReader;
import org.libav.LibavException;
import org.libav.audio.Frame2AudioFrameAdapter;
import org.libav.audio.PlaybackMixer;
import org.libav.audio.SampleInputStream;
import org.libav.avcodec.ICodecContextWrapper;
import org.libav.avformat.IStreamWrapper;
import org.libav.avutil.bridge.AVChannelLayout;
import org.libav.avutil.bridge.AVSampleFormat;

import test.jlibav.TestVideoPane;

/**
 * Enother usage example. It shows how to play some video. It uses features
 * shown in the previous example.
 * 
 * @author Ondrej Perutka
 */
public class SimpleVideoPlayer {
    
    private IMediaPlayer player;
    private SampleInputStream sampleInputStream;
    private AudioInputStream audioInputStream;
    private Frame2AudioFrameAdapter audioFrameAdapter;
    private PlaybackMixer mixer;
    private VideoPlayerFrame videoFrame;
    
    public SimpleVideoPlayer(String url) throws LibavException {
        // Open the given file/stream.
        player = new DefaultMediaPlayer(url);
    }
    
    /**
     * Start video playback.
     * 
     * @throws Exception if something goes wrong
     */
    public void play() throws Exception {
        // Get media player's reader.
        IMediaReader reader = player.getMediaReader();
        
        // Check if there is anything to play.
        if (reader.getAudioStreamCount() == 0 && reader.getVideoStreamCount() == 0)
            throw new Exception("there is nothing to play");
        
        // Prepare the first audio stream to be played. The audio stream will be 
        // converted and played as a 16 bit stereo.
        // NOTE: upmixing/downmixing (i.e. audio channel conversion) is not
        // supported without avresample library!!!
        if (reader.getAudioStreamCount() != 0)
            openAudioStream(0, AVChannelLayout.AV_CH_LAYOUT_STEREO, AVSampleFormat.AV_SAMPLE_FMT_S16);
        
        // Prepare the first video stream to be played.
        if (reader.getVideoStreamCount() != 0)
            openVideoStream(0);
        
        // Start playback and wait until it stops.
        player.play();
        player.join();
        
        // Flush the rest of audio data.
        sampleInputStream.flushBuffer();
    }
    
    /**
     * Release all resources.
     * 
     * @throws Exception if something goes wrong
     */
    public void close() throws Exception {
        player.close();
        
        if (audioFrameAdapter != null)
            audioFrameAdapter.dispose();
        if (sampleInputStream != null)
            sampleInputStream.close();
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
        
        // Enable decoding of the given video stream.
        player.setVideoStreamDecodingEnabled(videoStreamIndex, true);
    }
    
    /**
     * Prepare audio stream for playback.
     * 
     * @param audioStreamIndex audio stream index
     * @param outputChannelLayout output channel layout
     * @param outputSampleFormat output sample format
     * @throws Exception if something goes wrong
     */
    private void openAudioStream(int audioStreamIndex, long outputChannelLayout, int outputSampleFormat) throws Exception {
        // Get decoder of the given audio stream and its codec context.
        IDecoder decoder = player.getAudioStreamDecoder(audioStreamIndex);
        ICodecContextWrapper codecContext = decoder.getCodecContext();
        
        // Get some audio stream properties.
        int outputChannelCount = AVChannelLayout.getChannelCount(outputChannelLayout);
        int inputSampleRate = codecContext.getSampleRate();
        long inputChannelLayout = codecContext.getChannelLayout();
        
        if (inputChannelLayout == 0)
            inputChannelLayout = AVChannelLayout.getDefaultChannelLayout(codecContext.getChannels());
        
        // Get Java Sound API AudioFormat descriptor for the output audio 
        // format.
        AudioFormat audioFormat = getAudioFormat(outputChannelCount, inputSampleRate, outputSampleFormat);
        
        // Create adapter between Libav audio frames and intermediary 
        // objects suitable for the SampleInputStream.
        audioFrameAdapter = new Frame2AudioFrameAdapter(inputChannelLayout, 
                outputChannelLayout, inputSampleRate, inputSampleRate, 
                codecContext.getSampleFormat(), outputSampleFormat);
        
        // Create a sample input stream.
        sampleInputStream = new SampleInputStream(inputSampleRate * outputChannelCount * 
                AVSampleFormat.getBytesPerSample(outputSampleFormat), true);
        
        // Create Java Sound API AudioInputStream for the SampleInputStream.
        audioInputStream = new AudioInputStream(sampleInputStream, audioFormat, -1);
        
        // Get a playback mixer for the specified audio format.
        mixer = PlaybackMixer.getMixer(audioFormat);

        // Create links between the decoder, sample input stream and mixer.
        decoder.addFrameConsumer(audioFrameAdapter);
        audioFrameAdapter.addAudioFrameConsumer(sampleInputStream);
        mixer.addInputStream(audioInputStream);

        // Enable decoding of the given audio stream.
        player.setAudioStreamDecodingEnabled(audioStreamIndex, true);
        
        // Start sending audio data to the hardware layer.
        mixer.play();
    }
    
    /**
     * Create Java Sound API audio format descriptor for the given audio stream
     * params.
     * 
     * @param channelCount number of channels
     * @param sampleRate sample rate
     * @param sampleFormat sample format
     * @return audio format descriptor
     */
    private AudioFormat getAudioFormat(int channelCount, int sampleRate, int sampleFormat) {
        // Get sample width for the given sample format.
        int bytesPerSample = AVSampleFormat.getBytesPerSample(sampleFormat);
        
        AudioFormat.Encoding encoding;
        
        if (AVSampleFormat.isPlanar(sampleFormat) || AVSampleFormat.isReal(sampleFormat))
            throw new IllegalArgumentException("unsupported output sample format");
        else if (AVSampleFormat.isSigned(sampleFormat))
            encoding = AudioFormat.Encoding.PCM_SIGNED;
        else
            encoding = AudioFormat.Encoding.PCM_UNSIGNED;
        
        return new AudioFormat(encoding, sampleRate, bytesPerSample * 8, 
                channelCount, bytesPerSample * channelCount, sampleRate, 
                ByteOrder.BIG_ENDIAN.equals(ByteOrder.nativeOrder()));
    }
    
    public static void main(String[] args) {
        SimpleVideoPlayer simpleVideoPlayer = null;
        
        try {
            // Get a video file name or stream URL from command line args.
            String url = parseArgs(args);
            // Create a new instance of this simple video player.
            simpleVideoPlayer = new SimpleVideoPlayer(url);
            // Start playback.
            simpleVideoPlayer.play();
        } catch (Exception ex) {
            Logger.getLogger(SimpleVideoPlayer.class.getName()).log(Level.SEVERE, "something's wrong", ex);
        } finally {
            try {
                // We should always release kept resources if we don't need them.
                if (simpleVideoPlayer != null)
                    simpleVideoPlayer.close();
            } catch (Exception ex) {
                Logger.getLogger(SimpleVideoPlayer.class.getName()).log(Level.SEVERE, "unable to release system resources", ex);
            }
        }
    }
    
    /**
     * Check command line arguments and return the first one.
     * 
     * @param args command line arguments
     * @return first argument
     * @throws Exception if there is not exactly one argument
     */
    private static String parseArgs(String[] args) {
        if (args.length != 1)
            throw new IllegalArgumentException("USAGE: java -jar quick-start-03.jar file-name");
        
        return args[0];
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
