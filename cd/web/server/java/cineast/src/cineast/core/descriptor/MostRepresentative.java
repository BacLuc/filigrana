package cineast.core.descriptor;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.Frame;
import cineast.core.data.MultiImage;
import cineast.core.data.Shot;

public class MostRepresentative {

	private static final Logger LOGGER = LogManager.getLogger();
	
	private MostRepresentative(){}
	
	public static Frame getMostRepresentative(Shot shot){
		LOGGER.entry();
		MultiImage reference = shot.getAvgImg();
		Frame _return = null;
		double minDist = Double.POSITIVE_INFINITY;
		for(Frame f : shot.getFrames()){
			double dist = ImageDistance.colorDistance(reference, f.getImage());
			if(dist < minDist){
				minDist = dist;
				_return = f;
			}
		}
		return LOGGER.exit(_return);
	}
	
}
