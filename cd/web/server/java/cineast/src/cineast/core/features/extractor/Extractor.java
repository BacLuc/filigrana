package cineast.core.features.extractor;

import java.io.File;

import cineast.core.data.FrameContainer;
import cineast.core.data.MultiImage;
import cineast.core.data.Shot;
import cineast.core.db.PersistencyWriter;

public interface Extractor {

	void init(PersistencyWriter<?> phandler);
	
	void processShot(FrameContainer shot);
	
	
	//void processImage(File file);
		
	void finish();
}
