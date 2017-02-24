package cineast.core.segmenter;

import java.awt.image.BufferedImage;

import cineast.core.color.ColorConverter;
import cineast.core.color.FuzzyColorHistogramQuantizer;
import cineast.core.color.ReadableLabContainer;
import cineast.core.data.Frame;

public class FuzzyColorHistogramCalculator {

	private FuzzyColorHistogramCalculator(){}
	
	public static FuzzyColorHistogram getHistogram(Frame f){
		return getHistogram(f.getImage().getBufferedImage());
	}
	
	public static FuzzyColorHistogram getHistogram(BufferedImage img){
		int[] colors = img.getRGB(0, 0, img.getWidth(), img.getHeight(), null, 0, img.getWidth());
		return getHistogram(colors);
	}
	
	public static FuzzyColorHistogram getHistogram(int[] colors){
		FuzzyColorHistogram _return = new FuzzyColorHistogram();
		for(int color : colors){
			ReadableLabContainer lab = ColorConverter.cachedRGBtoLab(color);
			FuzzyColorHistogramQuantizer.Color c = FuzzyColorHistogramQuantizer.quantize(lab);
			_return.add(c);
		}
		return _return;
	}
	
	public static FuzzyColorHistogram getHistogramNormalized(Frame f){
		FuzzyColorHistogram _return = getHistogram(f);
		_return.normalize();
		return _return;
	}
	
	public static FuzzyColorHistogram getHistogramNormalized(BufferedImage img){
		FuzzyColorHistogram _return = getHistogram(img);
		_return.normalize();
		return _return;
	}
	
	public static SubdividedFuzzyColorHistogram getSubdividedHistogram(BufferedImage img, int subdivisions){
		int width = img.getWidth() / subdivisions, height = img.getHeight() / subdivisions;
		SubdividedFuzzyColorHistogram hist = new SubdividedFuzzyColorHistogram(subdivisions);
		for(int x = 0; x < subdivisions; ++x){
			for(int y = 0; y < subdivisions; ++y){
				int[] colors = img.getRGB(x * width, y * height, width, height, null, 0, width);
				for(int color : colors){
					ReadableLabContainer lab = ColorConverter.cachedRGBtoLab(color);
					FuzzyColorHistogramQuantizer.Color c = FuzzyColorHistogramQuantizer.quantize(lab);
					hist.add(c, x * subdivisions + y);
				}
			}
		}
		return hist;
	}
	
	public static SubdividedFuzzyColorHistogram getSubdividedHistogramNormalized(BufferedImage img, int subdivisions){
		SubdividedFuzzyColorHistogram hist = getSubdividedHistogram(img, subdivisions);
		hist.normalize();
		return hist;
	}

	public static FuzzyColorHistogram getHistogramNormalized(int[] colors) {
		FuzzyColorHistogram hist = getHistogram(colors);
		hist.normalize();
		return hist;
	}
}
