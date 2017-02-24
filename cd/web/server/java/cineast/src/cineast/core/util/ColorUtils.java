package cineast.core.util;

import java.util.List;

import cineast.core.color.LabContainer;
import cineast.core.color.RGBContainer;
import cineast.core.color.ReadableLabContainer;
import cineast.core.data.FloatVector;
import cineast.core.data.ReadableFloatVector;

public class ColorUtils {
	private ColorUtils(){}
	
	public static final ReadableLabContainer getAvg(List<ReadableLabContainer> containers){
		int size = containers.size();
		if(size == 0){
			return new LabContainer(0, 0, 0);
		}
		double L = 0, a = 0, b = 0;
		for(ReadableLabContainer container : containers){
			L += container.getL();
			a += container.getA();
			b += container.getB();
		}
		
		return new LabContainer(L / size, a / size, b / size);
	}
	
	public static final int getAvg(int[] colors){
		
		if(colors.length == 0){
			return 0;
		}
		
		int r = 0, g = 0, b = 0;
		for(int color : colors){
			r += RGBContainer.getRed(color);
			g += RGBContainer.getGreen(color);
			b += RGBContainer.getBlue(color);
		}
		
		float len = (float) colors.length;
		
		return RGBContainer.toIntColor(Math.round(r / len), Math.round(g / len), Math.round(b / len));
	}
	
	public static final int median(Iterable<Integer> colors){
		int[] histR = new int[256], histG = new int[256], histB = new int[256];
		for(int c : colors){
			histR[RGBContainer.getRed(c)]++;
			histG[RGBContainer.getGreen(c)]++;
			histB[RGBContainer.getBlue(c)]++;
		}
		return RGBContainer.toIntColor(medianFromHistogram(histR), medianFromHistogram(histG), medianFromHistogram(histB));
	}
	
	private static int medianFromHistogram(int[] hist){
		int pos_l = 0, pos_r = hist.length - 1;
		int sum_l = hist[pos_l], sum_r = hist[pos_r];
		
		while(pos_l < pos_r){
			if(sum_l < sum_r){
				sum_l += hist[++pos_l];
			}else{
				sum_r += hist[--pos_r];
			}
		}
		return pos_l;
	}
	
	public static final int getAvg(Iterable<Integer> colors){
				
		int r = 0, g = 0, b = 0, c = 0;
		for(int color : colors){
			r += RGBContainer.getRed(color);
			g += RGBContainer.getGreen(color);
			b += RGBContainer.getBlue(color);
			c++;
		}
		
		if(c == 0){
			return 0;
		}
		
		float len = (float)c;
		
		return RGBContainer.toIntColor(Math.round(r / len), Math.round(g / len), Math.round(b / len));
	}
	
	public static final FloatVector getAvg(List<? extends ReadableFloatVector> vectors, FloatVector result){
		int size = vectors.size();
		if(size == 0){
			for(int i = 0; i < result.getElementCount(); ++i){
				result.setElement(i, 0f);
			}
			return result;
		}
		double[] sum = new double[result.getElementCount()];
		for(ReadableFloatVector vector : vectors){
			for(int i = 0; i < sum.length; ++i){
				sum[i] += vector.getElement(i);
			}
		}
		
		for(int i = 0; i < sum.length; ++i){
			result.setElement(i, (float) (sum[i] / size));
		}
		
		return result;
	}
}
