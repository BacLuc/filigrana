package cineast.core.descriptor;

import java.util.List;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.TimeUnit;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import boofcv.alg.feature.detect.edge.CannyEdge;
import boofcv.alg.feature.detect.edge.EdgeContour;
import boofcv.core.image.ConvertBufferedImage;
import boofcv.factory.feature.detect.edge.FactoryEdgeDetectors;
import boofcv.struct.image.ImageSInt16;
import boofcv.struct.image.ImageUInt8;
import cineast.core.config.Config;
import cineast.core.data.MultiImage;

import com.google.common.cache.CacheBuilder;
import com.google.common.cache.CacheLoader;
import com.google.common.cache.LoadingCache;

public class EdgeList {

	private EdgeList() {
	}

	private static final Logger LOGGER = LogManager.getLogger();
	private static final float THRESHOLD_LOW = 0.1f, THRESHOLD_HIGH = 0.3f;

	public static List<EdgeContour> getEdgeList(MultiImage img){
		LOGGER.entry();
		ImageUInt8 gray = ConvertBufferedImage.convertFrom(img.getBufferedImage(), (ImageUInt8) null);
		CannyEdge<ImageUInt8, ImageSInt16> canny = getCanny();
		canny.process(gray, THRESHOLD_LOW, THRESHOLD_HIGH, null);
		List<EdgeContour> _return = canny.getContours();
		LOGGER.exit();
		return _return;
	}
	
	private static LoadingCache<Thread, CannyEdge<ImageUInt8, ImageSInt16>> cannies = CacheBuilder
			.newBuilder()
			.maximumSize(Config.numbetOfPoolThreads() * 2)
			.expireAfterAccess(10, TimeUnit.MINUTES)
			.build(new CacheLoader<Thread, CannyEdge<ImageUInt8, ImageSInt16>>() {

				@Override
				public CannyEdge<ImageUInt8, ImageSInt16> load(Thread arg0) {
					return FactoryEdgeDetectors.canny(2, true, true,
							ImageUInt8.class, ImageSInt16.class);
				}
			});

	private static synchronized CannyEdge<ImageUInt8, ImageSInt16> getCanny() {
		Thread current = Thread.currentThread();
		try {
			return cannies.get(current);
		} catch (ExecutionException e) {
			return null; // NEVER HAPPENS
		}
	}

}
