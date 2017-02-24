package cineast.core.run;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.List;

import javax.imageio.ImageIO;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.api.API;
import cineast.api.JSONUtils;
import cineast.core.data.LongDoublePair;
import cineast.core.data.MultiImage;
import cineast.core.data.MultiImageFactory;
import cineast.core.data.QueryContainer;
import cineast.core.db.FiligranaLookup;
import cineast.core.descriptor.EdgeImg;
import cineast.core.runtime.QueryDispatcher;
import cineast.core.util.LogHelper;

public class RetrievalRunner {
	private static final Logger LOGGER = LogManager.getLogger();
	private MultiImage img;
	private String path;
	private boolean isWatermark;
	private boolean withSketches;
	
	public RetrievalRunner(String path, boolean wm, boolean withSketches){
		this.path = path;
		this.isWatermark = wm;
		this.withSketches = withSketches;
	}
	
	/**
	 * extracts the features of the given file
	 * 
	 * @param path path of the file with the sketch in in to search
	 * @param wm if watermarks should be searched or iph_types
	 * @param withSketches if sketches should be used also
	 * @return json string of the results with wm/iph id and rank and much shit
	 */
	public String retrieve(){
		
		File file = new File(this.path);
		BufferedImage imgbuf;
		try {
			imgbuf = ImageIO.read(file);
			MultiImage img = MultiImageFactory.newMultiImage(imgbuf);
			img = EdgeImg.turnTransparentIntoWhite(img);
			QueryContainer qc = new QueryContainer(img);
			qc.setWatermark(this.isWatermark);
			
			QueryDispatcher dispatcher = new QueryDispatcher(API.getRetrievers()
					, API.getInitializer(), qc);
			
			List<LongDoublePair> result = dispatcher.call();
			FiligranaLookup sl = new FiligranaLookup();
			sl.setWatermark(this.isWatermark);
			String output = JSONUtils.resultToJSONString(result, sl);
			return output;
			
		} catch (IOException e) {
			// TODO Auto-generated catch block
			LOGGER.debug(LogHelper.getStackTrace(e));
			return "";
		}
		
	}
	
	
	public String retrieve(long shotid){
		QueryDispatcher dispatcher = new QueryDispatcher(API.getRetrievers()
				, API.getInitializer(), shotid);
		
		List<LongDoublePair> result = dispatcher.call();
		FiligranaLookup sl = new FiligranaLookup();
		sl.setWatermark(this.isWatermark);
		String output = JSONUtils.resultToJSONString(result, sl);
		return output;
	}
}
