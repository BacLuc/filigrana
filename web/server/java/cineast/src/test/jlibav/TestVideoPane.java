package test.jlibav;

import java.awt.Color;
import java.awt.Graphics;
import java.awt.Insets;
import java.awt.Point;
import java.awt.image.BufferedImage;
import java.awt.image.DataBuffer;
import java.awt.image.DataBufferInt;
import java.awt.image.DirectColorModel;
import java.awt.image.Raster;
import java.awt.image.SampleModel;
import java.awt.image.SinglePixelPackedSampleModel;
import java.awt.image.WritableRaster;
import java.nio.ByteOrder;
import java.util.logging.Level;
import java.util.logging.Logger;

import javax.swing.JComponent;

import org.bridj.Pointer;
import org.libav.LibavException;
import org.libav.avcodec.FrameWrapperFactory;
import org.libav.avcodec.IFrameWrapper;
import org.libav.avutil.bridge.PixelFormat;
import org.libav.data.IFrameConsumer;
import org.libav.swscale.ScaleContextWrapper;
import org.libav.swscale.bridge.SWScaleLibrary;

/**
 * SWING component for video rendering.
 * 
 * @author Ondrej Perutka
 */
public class TestVideoPane extends JComponent implements IFrameConsumer {

//    /**
//     * Choose the best output mode for current platform.
//     */
//    public static final int OUTPUT_DEFAULT = 0;
//    
//    /**
//     * Output video frames using SWING.
//     */
//    public static final int OUTPUT_SWING = 1;
//    
//    /**
//     * Output video frames using XVideo (if available).
//     */
//    public static final int OUTPUT_XV = 2;
//    
//    /**
//     * Output video frames using DirectDraw (if available).
//     */
//    public static final int OUTPUT_DDRAW = 3;
    
    private ScaleContextWrapper scaleContext;
    private IFrameWrapper rgbFrame;
    private Pointer<Byte> rgbFrameData;
    private BufferedImage img;
    private int[] imageData;

    private int x;
    private int y;
    private int srcWidth;
    private int srcHeight;
    private int srcPixelFormat;
    private int dstWidth;
    private int dstHeight;
    private int dstPixelFormat;
    private int scalingAlgorithm;
    
    private Insets insts;
    
    /**
     * Create a new video pane.
     */
    public TestVideoPane() {
        setBackground(Color.black);
        setOpaque(true);
        
        scaleContext = null;
        rgbFrame = null;
        rgbFrameData = null;
        img = null;
        imageData = null;
        
        x = 0;
        y = 0;
        srcWidth = 0;
        srcHeight = 0;
        srcPixelFormat = PixelFormat.PIX_FMT_YUV420P;
        scalingAlgorithm = SWScaleLibrary.SWS_BICUBIC;
        dstWidth = 0;
        dstHeight = 0;
        dstPixelFormat = PixelFormat.PIX_FMT_BGRA;
        if (ByteOrder.BIG_ENDIAN.equals(ByteOrder.nativeOrder()))
            dstPixelFormat = PixelFormat.PIX_FMT_ARGB;
        
        insts = null;

//        addComponentListener(new ResizeHandler());
    }

    @Override
    public boolean isOptimizedDrawingEnabled() {
        return true;
    }

    @Override
    public synchronized void paintComponent(Graphics grphcs) {
        Color prev = grphcs.getColor();
        
        grphcs.setColor(getBackground());
        grphcs.fillRect(0, 0, getWidth(), getHeight());
        grphcs.setColor(prev);
        
        if (scaleContext != null)
            grphcs.drawImage(img, x, y, dstWidth, dstHeight, this);
    }
    
    /**
     * Get the X position of the last frame inside this component.
     * 
     * @return X position of the last frame
     */
    public int getFrameX() {
        return x;
    }
    
    /**
     * Get the Y position of the last frame inside this component.
     * 
     * @return Y position of the last frame
     */
    public int getFrameY() {
        return y;
    }
    
    /**
     * Get width of the last frame inside this component.
     * 
     * @return width of the last frame
     */
    public int getFrameWidth() {
        return dstWidth > 0 ? dstWidth : getWidth();
    }
    
    /**
     * Get height of the last frame inside this component.
     * 
     * @return height of the last frame
     */
    public int getFrameHeight() {
        return dstHeight > 0 ? dstHeight : getHeight();
    }
    
    /**
     * Get expected source frame width (default value is 0).
     * 
     * @return expected source frame width
     */
    public int getSourceWidth() {
        return srcWidth;
    }
    
    /**
     * Get expected source frame height (default value is 0).
     * 
     * @return expected source frame height
     */
    public int getSourceHeight() {
        return srcHeight;
    }
    
    /**
     * Get expected source frame pixel format.
     * 
     * @return expected source frame pixel format
     */
    public int getSourcePixelFormat() {
        return srcPixelFormat;
    }
    
    /**
     * Get ID of the selected scaling algorithm (default selection is 
     * FAST_BILINEAR).
     * 
     * @return ID of the selected scaling algorithm
     */
    public int getScalingAlgorithm() {
        return scalingAlgorithm;
    }
    
    /**
     * Set expected format of source images.
     * 
     * @param width a width
     * @param height a height
     * @param pixelFormat a pixel format
     */
    public synchronized void setSourceImageFormat(int width, int height, int pixelFormat) {
        if (width <= 0 || height <= 0)
            throw new IllegalArgumentException("illegal frame size");
        
        srcWidth = width;
        srcHeight = height;
        srcPixelFormat = pixelFormat;
        
        resetScaleContext();
    }
    
    /**
     * Set scaling algorithm.
     * 
     * @param scalingAlgorithm a scaling algorithm
     */
    public synchronized void setScalingAlgorithm(int scalingAlgorithm) {
        this.scalingAlgorithm = scalingAlgorithm;
        resetScaleContext();
    }
    
    private synchronized void resetScaleContext() {
        disposeScaleContext();
        
        if (srcWidth <= 0 || srcHeight <= 0)
            return;
        
        insts = getInsets(insts);
        int w = getWidth() - insts.left - insts.right;
        int h = getHeight() - insts.top - insts.bottom;
        double r = Math.min((double)w / srcWidth, (double)h / srcHeight);
        
        x = insts.left + (int)((w - srcWidth * r) / 2);
        y = insts.top + (int)((h - srcHeight * r) / 2);
        dstWidth = (int)(srcWidth * r);
        dstHeight = (int)(srcHeight * r);
        
        try {
            scaleContext = ScaleContextWrapper.createContext(srcWidth, srcHeight, srcPixelFormat, dstWidth, dstHeight, dstPixelFormat, scalingAlgorithm);
            rgbFrame = FrameWrapperFactory.getInstance().allocPicture(dstPixelFormat, dstWidth, dstHeight);
            rgbFrameData = rgbFrame.getData().get();
        } catch (LibavException ex) {
            Logger.getLogger(TestVideoPane.class.getName()).log(Level.SEVERE, "unable initialize video pane scaling context", ex);
            if (scaleContext != null)
                scaleContext.free();
            scaleContext = null;
            return;
        }
        
        imageData = new int[dstWidth * dstHeight];
        DataBuffer db = new DataBufferInt(imageData, imageData.length);
        int[] masks = new int[] { 0x00ff0000, 0x0000ff00, 0x000000ff };
        SampleModel sm = new SinglePixelPackedSampleModel(DataBuffer.TYPE_INT, dstWidth, dstHeight, masks);
        WritableRaster wr = Raster.createWritableRaster(sm, db, new Point());
        img = new BufferedImage(new DirectColorModel(24, 0x00ff0000, 0x0000ff00, 0x000000ff), wr, false, null);
    }
    
    private synchronized void disposeScaleContext() {
        if (scaleContext != null)
            scaleContext.free();
        if (rgbFrame != null)
            rgbFrame.free();
        
        scaleContext = null;
    }
    
    /**
     * Dispose all resources held by this object.
     */
    public void dispose() {
        disposeScaleContext();
    }

    /**
     * Clear the component with the background color.
     */
    public synchronized void clear() {
        if (scaleContext != null) {
            for (int i = 0; i < imageData.length; i++)
                imageData[i] = 0;
        }
        
        repaint();
    }
    
    @Override
    public synchronized void processFrame(Object producer, IFrameWrapper frame) {
    	System.out.println("TestVideoPane.processFrame()");
    	System.out.println(producer);
    	System.out.println(frame);
        if (scaleContext == null)
            return;
        
        try {
            scaleContext.scale(frame, rgbFrame, 0, srcHeight);
                        
            rgbFrameData.getIntsAtOffset(0, imageData, 0, imageData.length);
            repaint();
        } catch (LibavException ex) {
            Logger.getLogger(TestVideoPane.class.getName()).log(Level.WARNING, "video pane has uninitielized source image format", ex);
        }
        
//        try {
//			Thread.sleep(1000);
//		} catch (InterruptedException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//		}
        
    }
    
//    private class ResizeHandler extends ComponentAdapter {
//        @Override
//        public void componentResized(ComponentEvent e) {
//            resetScaleContext();
//            repaint();
//        }
//    }
    
}
