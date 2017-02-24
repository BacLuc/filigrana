package test;

import java.awt.BorderLayout;
import java.awt.Dimension;

import javax.swing.JFrame;

import test.jlibav.TestVideoPane;



/**
 * Simple SWING frame used to show video frames on it.
 *
 * @author Ondrej Perutka
 */
public class VideoPlayerFrame extends JFrame {

    private TestVideoPane videoPane;
   
    /**
     * Create a new video player frame, set the window title and dimension
     * of the video pane.
     *
     * @param windowTitle window title
     * @param width video pane width
     * @param height video pane hight
     */
    public VideoPlayerFrame(String windowTitle, int width, int height) {
        super(windowTitle);
       
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        
        videoPane = new TestVideoPane();
        videoPane.setPreferredSize(new Dimension(width, height));
       
        setLayout(new BorderLayout());
       
        add(videoPane, BorderLayout.CENTER);
       
        pack();
    }

    /**
     * Get video pane.
     *
     * @return video pane
     */
    public TestVideoPane getVideoPane() {
        return videoPane;
    }

    @Override
    public void dispose() {
        videoPane.dispose();
        super.dispose();
    }
   
}

