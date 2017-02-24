package cineast.core.runtime;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.FrameContainer;
import cineast.core.features.extractor.Extractor;
import cineast.core.util.DecodingError;

class ExtractionTask implements Runnable {

	private Extractor feature;
	private FrameContainer shot;
	private static final Logger LOGGER = LogManager.getLogger();
	
	ExtractionTask(Extractor feature, FrameContainer shot) {
		this.feature = feature;
		this.shot = shot;
	}
	
	@Override
	public void run() {
		LOGGER.entry();
		LOGGER.debug("starting {} on shotId {}", feature.getClass().getSimpleName(), shot.getId());
		try{
			feature.processShot(shot);
		}catch(DecodingError e){
			LOGGER.fatal("DECODING ERROR");
			throw e;
		}
		LOGGER.exit();
	}

}
