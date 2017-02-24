package cineast.core.descriptor;

import java.util.Arrays;
import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.color.RGBContainer;
import cineast.core.data.Frame;
import cineast.core.data.MultiImage;
import cineast.core.data.MultiImageFactory;
import cineast.core.util.DecodingError;
/**
 * WARNING: EXTREMELY SLOW
 * 
 * @author Luca Rossetto
 *
 */
public class MedianImg {

	private static final Logger LOGGER = LogManager.getLogger();
	
	private MedianImg(){}
	
	public static MultiImage getMedian(List<Frame> frames){
		
		LOGGER.entry();
		
		MultiImage first = frames.get(0).getImage();
		int width = first.getWidth(), height = first.getHeight();
		
		short[] buffer = new short[width * height * 128];
		
		int[] result = new int[width * height];
		
		//red pass
		try{
			for(Frame f : frames){
				int[] colors = f.getImage().getColors();
				for(int i = 0; i < colors.length; ++i){
					buffer[128 * i + (RGBContainer.getRed(colors[i])) / 2]++;
				}
				colors = null;
			}
		}catch(Exception e){
			throw new DecodingError();
		}
		short[] hist = new short[128];
		
		for(int i = 0; i < result.length; ++i){
			System.arraycopy(buffer, i * 128, hist, 0, 128);
			int r = medianFromHistogram(hist) * 2;
			result[i] = r;
		}
		
		//green pass
		
		Arrays.fill(buffer, (short)0);
		try{
			for(Frame f : frames){
				int[] colors = f.getImage().getColors();
				for(int i = 0; i < colors.length; ++i){
					buffer[128 * i + (RGBContainer.getGreen(colors[i])) / 2]++;
				}
				colors = null;
			}
		}catch(Exception e){
			throw new DecodingError();
		}
		
		for(int i = 0; i < result.length; ++i){
			System.arraycopy(buffer, i * 128, hist, 0, 128);
			int r = result[i];
			int g = medianFromHistogram(hist) * 2;
			result[i] = RGBContainer.toIntColor(r, g, 0);
		}
		
		//blue pass
		Arrays.fill(buffer, (short)0);
		try{
			for(Frame f : frames){
				int[] colors = f.getImage().getColors();
				for(int i = 0; i < colors.length; ++i){
					buffer[128 * i + (RGBContainer.getBlue(colors[i])) / 2]++;
				}
				colors = null;
			}
		}catch(Exception e){
			throw new DecodingError();
		}
		
		for(int i = 0; i < result.length; ++i){
			System.arraycopy(buffer, i * 128, hist, 0, 128);
			int r = RGBContainer.getRed(result[i]);
			int g = RGBContainer.getGreen(result[i]);
			int b = medianFromHistogram(hist) * 2;
			result[i] = RGBContainer.toIntColor(r, g, b);
		}
		
		buffer = null;
		
		System.gc();
		LOGGER.exit();
		return MultiImageFactory.newMultiImage(width, height, result);
		
		
//		System.out.println("MedianImg.getMedian()");
//		
//		MultiImage first = frames.get(0).getImage();
//		int width = first.getWidth(), height = first.getHeight();
//		FileCachedImageHistogram fcih = new FileCachedImageHistogram(width, height);
//		for(Frame frame : frames){
//			BufferedImage bimg = frame.getImage().getBufferedImage();
//			for(int y = 0; y < height; ++y){
//				for(int x = 0; x < width; ++x){
//					int col = bimg.getRGB(x, y);
//					fcih.updateBucket(x, y, 0, RGBContainer.getRed(col));
//					fcih.updateBucket(x, y, 1, RGBContainer.getGreen(col));
//					fcih.updateBucket(x, y, 2, RGBContainer.getBlue(col));
//				}
//				System.err.println(y);
//			}
//		}
//		
//		BufferedImage median = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
//		for(int y = 0; y < height; ++y){
//			for(int x = 0; x < width; ++x){
//				int r = medianFromHistogram(fcih.getBucket(x, y, 0));
//				int g = medianFromHistogram(fcih.getBucket(x, y, 1));
//				int b = medianFromHistogram(fcih.getBucket(x, y, 2));
//				median.setRGB(x, y, RGBContainer.toIntColor(r, g, b));
//			}
//			System.err.println(y);
//		}
//		
//		return new MultiImage(median);
	}
	
	private static int medianFromHistogram(short[] hist){
		int pos_l = 0, pos_r = hist.length - 1;
		int sum_l = uShortToInt(hist[pos_l]), sum_r = uShortToInt(hist[pos_r]);
		
		while(pos_l < pos_r){
			if(sum_l < sum_r){
				sum_l += uShortToInt(hist[++pos_l]);
			}else{
				sum_r += uShortToInt(hist[--pos_r]);
			}
		}
		return pos_l;
	}
	
	private static int uShortToInt(short s){
		return (s % 0xFFFF);
	}
	
}
