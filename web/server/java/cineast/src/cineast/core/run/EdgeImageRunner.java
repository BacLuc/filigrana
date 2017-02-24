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

public class EdgeImageRunner {
	private static final Logger LOGGER = LogManager.getLogger();
	private MultiImage img;
	private String path;
	private boolean isWatermark;
	private boolean withSketches;
	
	public EdgeImageRunner(String path){
		this.path = path;
	}
	
	/**
	 * extracts the features of the given file
	 * 
	 * @param path path of the file with the sketch in in to search
	 * @param wm if watermarks should be searched or iph_types
	 * @param withSketches if sketches should be used also
	 * @return json string of the results with wm/iph id and rank and much shit
	 */
	public String makeEdgeImg(){
		
		File file = new File(this.path);
		
		BufferedImage imgbuf;
		try {
			imgbuf = ImageIO.read(file);
	
			MultiImage img = MultiImageFactory.newMultiImage(imgbuf);
			MultiImage edgeImage = EdgeImg.getEdgeImg(img);
			MultiImage blackEdges = edgeImage;//EdgeImg.switchBlackAndWhite(edgeImage);
			String newPath = file.getAbsolutePath().substring(0, file.getAbsolutePath().lastIndexOf("."));
			newPath += "edgeImage.png";
			ImageIO.write(blackEdges.getBufferedImage(), "png", new File(newPath));
			return newPath;
			
		} catch (IOException e) {
			// TODO Auto-generated catch block
			LOGGER.debug(LogHelper.getStackTrace(e));
			return "";
		}
		
	}
}
