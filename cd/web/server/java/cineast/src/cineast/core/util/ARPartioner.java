package cineast.core.util;

import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;

import cineast.core.color.ColorConverter;
import cineast.core.color.ReadableLabContainer;
import cineast.core.data.FloatVector;
import cineast.core.data.FloatVectorImpl;
import cineast.core.data.MultiImage;
import cineast.core.data.StatElement;

public final class ARPartioner {

	private ARPartioner(){}
	
	public static <T> ArrayList<LinkedList<T>> partition(List<T> input, int width, int height, int angularSegments, int radialSegments){
		ArrayList<LinkedList<T>> _return = new ArrayList<>(angularSegments * radialSegments);
		for(int i = 0; i < angularSegments * radialSegments; ++i){
			_return.add(new LinkedList<T>());
		}
		
		float centerX = width / 2f, centerY = height / 2f, w = (float) width, h = (float) height;
		
		for(int i = 0; i < input.size(); ++i){
			float   x = ((i % width) - centerX) / w,
					y = ((i / width) - centerY) / h;
			
			
			//to polar
			float r = (float) Math.sqrt(x * x + y * y);
			float phi = (float) (Math.atan2(y, x) + Math.PI);
			
			int radialSegment = r >= 0.5f ? radialSegments - 1 : (int) Math.floor(r * 2 * radialSegments);
			int angularSegment = ((int) Math.floor((phi / (2 * Math.PI)) * angularSegments)) % angularSegments;
			
			int index = radialSegment + radialSegments * angularSegment;
			
			_return.get(index).add(input.get(i));
			
		}
		
		return _return;
	}
	
	public static FloatVector partitionImage(MultiImage img, int angularSegments, int radialSegments){
		int[] colors = img.getColors();
		ArrayList<Integer> tmpList = new ArrayList<>(colors.length);
		for(int c : colors){
			tmpList.add(c);
		}
		
		ArrayList<LinkedList<Integer>> partitions = ARPartioner.partition(tmpList, img.getWidth(), img.getHeight(), angularSegments, radialSegments);
		ArrayList<StatElement> stats = new ArrayList<StatElement>(angularSegments * radialSegments * 3);
		
		for(int i = 0; i < angularSegments * radialSegments * 3; ++i){
			stats.add(new StatElement());
		}
		
		for(int i = 0; i < angularSegments * radialSegments; ++i){
			LinkedList<Integer> cols = partitions.get(i);
			StatElement L = stats.get(3 * i);
			StatElement a = stats.get(3 * i + 1);
			StatElement b = stats.get(3 * i + 2);
			
			for(int c : cols){
				ReadableLabContainer lab = ColorConverter.cachedRGBtoLab(c);
				L.add(lab.getL());
				a.add(lab.getA());
				b.add(lab.getB());
			}
		}
		
		float[] vec = new float[stats.size() * 2];
		
		for(int i = 0; i < stats.size(); ++i){
			StatElement s = stats.get(i);
			vec[2 * i] = s.getAvg();
			vec[2 * i + 1] = s.getVariance();
		}
		
		return new FloatVectorImpl(vec);
	}
	
}
