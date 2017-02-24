package cineast.core.descriptor;

import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.color.RGBContainer;
import cineast.core.data.CachedMultiImage;
import cineast.core.data.Frame;
import cineast.core.data.MultiImage;
import cineast.core.data.MultiImageFactory;
import cineast.core.util.DecodingError;
import cineast.core.util.TimeHelper;

public class AvgImg {

	private static final Logger LOGGER = LogManager.getLogger();
	
	private AvgImg(){}
	
	public static MultiImage getAvg(List<Frame> frames){
		TimeHelper.tic();
		LOGGER.entry();
		MultiImage first = frames.get(0).getImage();
		int width = first.getWidth(), height = first.getHeight();
		double[] buffer = new double[width * height * 3];
		int[] colors;
		try{
			for(Frame frame : frames){
				colors = frame.getImage().getColors();
				if((colors.length * 3) != buffer.length){
					throw new DecodingError();
				}
				for(int i = 0; i < colors.length; ++i){
					int col = colors[i];
					buffer[3*i]     += RGBContainer.getRed(col);
					buffer[3*i + 1] += RGBContainer.getGreen(col);
					buffer[3*i + 2] += RGBContainer.getBlue(col);
				}
			}
		}catch(Exception e){
			throw new DecodingError();
		}
		
		
		int size = frames.size();
		
		//BufferedImage _return = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
		
		colors = new int[width * height];
		
		for(int i = 0; i < colors.length; ++i){
			colors[i] = RGBContainer.toIntColor(
					(int)Math.round(buffer[3*i] / size),
					(int)Math.round(buffer[3*i + 1] / size),
					(int)Math.round(buffer[3*i + 2] / size));
		}
		
		//_return.setRGB(0, 0, width, height, colors, 0, width);
		
		//colors = null;
		buffer = null;
		
		System.gc();
		LOGGER.debug("AvgImg.getAvg() done in {}", TimeHelper.toc());
		LOGGER.exit();
		return MultiImageFactory.newMultiImage(width, height, colors);
	}
	
}
