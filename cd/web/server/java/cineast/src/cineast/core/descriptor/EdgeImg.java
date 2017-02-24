package cineast.core.descriptor;

import java.awt.image.BufferedImage;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.TimeUnit;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import boofcv.alg.feature.detect.edge.CannyEdge;
import boofcv.core.image.ConvertBufferedImage;
import boofcv.factory.feature.detect.edge.FactoryEdgeDetectors;
import boofcv.gui.binary.VisualizeBinaryData;
import boofcv.struct.image.ImageSInt16;
import boofcv.struct.image.ImageUInt8;
import cineast.core.config.Config;
import cineast.core.data.MultiImage;
import cineast.core.data.MultiImageFactory;

import com.google.common.cache.CacheBuilder;
import com.google.common.cache.CacheLoader;
import com.google.common.cache.LoadingCache;

public class EdgeImg {

	private EdgeImg() {
	}

	private static final Logger LOGGER = LogManager.getLogger();

	private static final float THRESHOLD_LOW = 0.075f, THRESHOLD_HIGH = 0.3f;

	//private static final CannyEdge<ImageUInt8, ImageSInt16> canny = FactoryEdgeDetectors.canny(2, false, true, ImageUInt8.class, ImageSInt16.class);
	
	public static MultiImage getEdgeImg(MultiImage img) {
		LOGGER.entry();

		ImageUInt8 gray = ConvertBufferedImage.convertFrom(img.getBufferedImage(), (ImageUInt8) null);
		if(!isSolidBlack(gray)){
			getCanny().process(gray, THRESHOLD_LOW, THRESHOLD_HIGH, gray);
		}

		BufferedImage bout = VisualizeBinaryData.renderBinary(gray, null);

		return LOGGER.exit(MultiImageFactory.newMultiImage(bout));
	}
	
	/**
	 * Switches every white pixel into black pixel and vice versa, and copies the other pixels
	 * @param BufferedImage image
	 * @return BufferedImage
	 */
	public static BufferedImage switchBlackAndWhite(BufferedImage image){
		BufferedImage returnImage = new BufferedImage(image.getWidth(), image.getHeight(), image.getType());
		int white = EdgeImg.colorToRGB(255, 255, 255, 255);
		int black = EdgeImg.colorToRGB(255, 0, 0, 0);
		for(int i = 0; i< image.getWidth(); i++){
			for(int j = 0; j < image.getHeight(); j++){
				int originalRGB = image.getRGB(i, j);
				int[] values = EdgeImg.RGBToIntArray(originalRGB);
				int alpha = values[0];
				int red =   values[1];
				int green = values[2];
				int blue =  values[3];
				
				if(red == 0 && green ==  0 && blue == 0){
					
					returnImage.setRGB(i, j, white);
				}else if(red == 255 && green ==  255 && blue == 255){
					returnImage.setRGB(i, j, black);
				}else{
					returnImage.setRGB(i, j, originalRGB);
				}
			}
		}
		return LOGGER.exit(returnImage);
	}
	
	/**
	 * Switches every white pixel into black pixel and vice versa, and copies the other pixels
	 * @param MultiImage image
	 * @return MultiImage
	 */
	public static MultiImage switchBlackAndWhite(MultiImage multimage){
		BufferedImage image = multimage.getBufferedImage();
		BufferedImage returnImage = new BufferedImage(image.getWidth(), image.getHeight(), image.getType());
		int white = EdgeImg.colorToRGB(255, 255, 255, 255);
		int black = EdgeImg.colorToRGB(255, 0, 0, 0);
		for(int i = 0; i< image.getWidth(); i++){
			for(int j = 0; j < image.getHeight(); j++){
				int originalRGB = image.getRGB(i, j);
				int[] values = EdgeImg.RGBToIntArray(originalRGB);
				int alpha = values[0];
				int red =   values[1];
				int green = values[2];
				int blue =  values[3];
				
				if(red == 0 && green ==  0 && blue == 0){
					
					returnImage.setRGB(i, j, white);
				}else if(red == 255 && green ==  255 && blue == 255){
					returnImage.setRGB(i, j, black);
				}else{
					returnImage.setRGB(i, j, originalRGB);
				}
			}
		}
		return LOGGER.exit(MultiImageFactory.newMultiImage(returnImage));
	}
	/**
	 * turns all transparent pixels into white pixels
	 * @param multimage
	 * @return
	 */
	public static MultiImage turnTransparentIntoWhite(MultiImage multimage){
		BufferedImage image = multimage.getBufferedImage();
		BufferedImage returnImage = new BufferedImage(image.getWidth(), image.getHeight(), image.getType());
		int white = EdgeImg.colorToRGB(255, 255, 255, 255);
		int black = EdgeImg.colorToRGB(255, 0, 0, 0);
		for(int i = 0; i< image.getWidth(); i++){
			for(int j = 0; j < image.getHeight(); j++){
				int originalRGB = image.getRGB(i, j);
				int[] values = EdgeImg.RGBToIntArray(originalRGB);
				int alpha = values[0];
				int red =   values[1];
				int green = values[2];
				int blue =  values[3];
				
				if(alpha == 0){
					
					returnImage.setRGB(i, j, white);
				}else{
					returnImage.setRGB(i, j, originalRGB);
				}
			}
		}
		return LOGGER.exit(MultiImageFactory.newMultiImage(returnImage));
	}
	
	
	/**
	 * converts the values into a TYPE_INT_ARGB representation like its used in BufferedImage
	 * @param alpha
	 * @param red
	 * @param green
	 * @param blue
	 * @return
	 */
	 private static int colorToRGB(int alpha, int red, int green, int blue) {
         int newPixel = 0;
         newPixel += alpha;
         newPixel = newPixel << 8;
         newPixel += red; newPixel = newPixel << 8;
         newPixel += green; newPixel = newPixel << 8;
         newPixel += blue;

         return newPixel;
     }
	 
	 /**
	  * converts the TYPE_INT_ARGB value into an int[4].
	  * int[0] is alpha
	  * int[1] is red
	  * int[2] is green
	  * int[3] is blue
	  * @param rgb
	  * @return
	  */
	 public static int[] RGBToIntArray(int rgb){
		 int[] returnArray = new int[4];
		 returnArray[0] = (rgb >> 24) & 0xFF;
		 returnArray[1]=   (rgb >> 16) & 0xFF;
		 returnArray[2]= (rgb >>  8) & 0xFF;
		 returnArray[3]=  (rgb      ) & 0xFF;
		 
		 return returnArray;
	 }

	public static boolean[] getEdgePixels(MultiImage img, boolean[] out) {
		LOGGER.entry();

		if (out == null || out.length != img.getWidth() * img.getHeight()) {
			out = new boolean[img.getWidth() * img.getHeight()];
		}

		ImageUInt8 gray = ConvertBufferedImage.convertFrom(img.getBufferedImage(), (ImageUInt8) null);

		if(!isSolidBlack(gray)){
			getCanny().process(gray, THRESHOLD_LOW, THRESHOLD_HIGH, gray);
			
		}

		for (int i = 0; i < gray.data.length; ++i) {
			out[i] = (gray.data[i] != 0);
		}

		LOGGER.exit();
		return out;
	}

	public static List<Boolean> getEdgePixels(MultiImage img, List<Boolean> out) {
		LOGGER.entry();
		if (out == null) {
			out = new ArrayList<Boolean>(img.getWidth() * img.getHeight());
		} else {
			out.clear();
		}
		
		ImageUInt8 gray = ConvertBufferedImage.convertFrom(img.getBufferedImage(), (ImageUInt8) null);
		if(!isSolidBlack(gray)){
			getCanny().process(gray, THRESHOLD_LOW, THRESHOLD_HIGH, gray);
		}

		for (int i = 0; i < gray.data.length; ++i) {
			out.add(gray.data[i] != 0);
		}
		LOGGER.exit();
		return out;
	}
	
	public static boolean isSolidBlack(ImageUInt8 img){
		for(byte b : img.data){
			if(b != 0){
				return false;
			}
		}
		return true;
	}
	
	//private static HashMap<Thread, CannyEdge<ImageUInt8, ImageSInt16>> cannies = new HashMap<Thread, CannyEdge<ImageUInt8,ImageSInt16>>();
	private static LoadingCache<Thread, CannyEdge<ImageUInt8, ImageSInt16>> cannies = CacheBuilder.newBuilder().maximumSize(Config.numbetOfPoolThreads() * 2)
			.expireAfterAccess(10, TimeUnit.MINUTES).build(new CacheLoader<Thread, CannyEdge<ImageUInt8, ImageSInt16>>(){

				@Override
				public CannyEdge<ImageUInt8, ImageSInt16> load(Thread arg0){
					return FactoryEdgeDetectors.canny(2, false, true, ImageUInt8.class, ImageSInt16.class);
				}});
	private static synchronized CannyEdge<ImageUInt8, ImageSInt16> getCanny(){
		Thread current = Thread.currentThread();
		try {
			return cannies.get(current);
		} catch (ExecutionException e) {
			return null; //NEVER HAPPENS
		}
	}
}
