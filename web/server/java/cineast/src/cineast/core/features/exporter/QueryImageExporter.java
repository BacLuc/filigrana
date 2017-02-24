package cineast.core.features.exporter;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.LinkedList;
import java.util.List;
import cineast.core.data.FrameContainer;
import javax.imageio.ImageIO;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.LongDoublePair;
import cineast.core.data.QueryContainer;
import cineast.core.db.DBSelector;
import cineast.core.features.retriever.Retriever;
import cineast.core.util.LogHelper;

public class QueryImageExporter implements Retriever {

	private File folder = new File("queryImages");
	private DateFormat df = new SimpleDateFormat("MM-dd-yyyy_HH-mm-ss-SSS");
	private static final Logger LOGGER = LogManager.getLogger();
	
	@Override
	public void init(DBSelector selector) {
		selector.close();
		if(!this.folder.exists() || !this.folder.isDirectory()) {
			this.folder.mkdirs();
		}
	}

	@Override
	public List<LongDoublePair> getSimilar(FrameContainer qc) {
		BufferedImage bimg = qc.getMostRepresentativeFrame().getImage().getBufferedImage();
		try {
			ImageIO.write(bimg, "PNG", new File(folder, this.df.format(Calendar.getInstance().getTime()) + ".png"));
		} catch (IOException e) {
			LOGGER.error(LogHelper.getStackTrace(e));
		}
		return new LinkedList<LongDoublePair>();
	}

	@Override
	public List<LongDoublePair> getSimilar(long shotId) {
		return new LinkedList<LongDoublePair>();
	}

	@Override
	public void finish() {
	}

}
