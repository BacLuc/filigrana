package cineast.core.features.exporter;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

import javax.imageio.ImageIO;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.FrameContainer;
import cineast.core.data.Shot;
import cineast.core.db.PersistencyWriter;
import cineast.core.features.extractor.Extractor;
import cineast.core.util.LogHelper;

public class ShotThumbNails implements Extractor {

	private File folder;
	private static final Logger LOGGER = LogManager.getLogger();
	
	@Override
	public void init(PersistencyWriter<?> phandler) {
		phandler.close(); //not needed
		this.folder = new File("thumbnails");
		if(!this.folder.exists()){
			this.folder.mkdirs();
		}
	}

	@Override
	public void processShot(FrameContainer shot) {
		
		File imageFolder = new File(this.folder, Long.toString(shot.getSuperId()));
		if(!imageFolder.exists()){
			imageFolder.mkdirs();
		}
		
		File img = new File(imageFolder, shot.getId() + ".png");
		if(img.exists()){
			return;
		}
		BufferedImage thumb = shot.getMostRepresentativeFrame().getImage().getThumbnailImage();
		try {
			ImageIO.write(thumb, "PNG", img);
		} catch (IOException e) {
			LOGGER.error("Could not write thumbnail image ", LogHelper.getStackTrace(e));
		}
		
	}

	@Override
	public void finish() {}

}
