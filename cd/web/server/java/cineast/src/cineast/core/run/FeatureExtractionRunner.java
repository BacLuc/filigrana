package cineast.core.run;

import java.awt.image.BufferedImage;

import com.google.common.io.Files; 

import java.io.File;
import java.io.FileFilter;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FilenameFilter;
import java.io.IOException;
import java.nio.file.StandardCopyOption;
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;

import javax.activation.MimetypesFileTypeMap;
import javax.imageio.ImageIO;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.apache.logging.log4j.core.util.FileUtils;
import org.omg.CORBA.portable.OutputStream;

import cineast.api.API;
import cineast.core.config.Config;
import cineast.core.data.MultiImage;
import cineast.core.data.MultiImageFactory;
import cineast.core.data.QueryContainer;
import cineast.core.data.providers.ShotProvider;
import cineast.core.db.ADAMTuple;
import cineast.core.db.ADAMWriter;
import cineast.core.db.ReturningADAMTuple;
import cineast.core.db.ShotLookup;
import cineast.core.db.ShotDescriptor;
import cineast.core.decode.subtitle.SubTitle;
import cineast.core.decode.subtitle.srt.SRTSubTitle;
import cineast.core.decode.video.JCodecVideoDecoder;
import cineast.core.decode.video.JLibAVVideoDecoder;
import cineast.core.decode.video.VideoDecoder;
import cineast.core.descriptor.EdgeImg;
import cineast.core.features.DominantEdgeGrid16;
import cineast.core.features.DominantEdgeGrid8;
import cineast.core.features.EHD;
import cineast.core.features.EdgeARP88;
import cineast.core.features.EdgeARP88Full;
import cineast.core.features.EdgeGrid16;
import cineast.core.features.EdgeGrid16Full;
import cineast.core.features.exporter.FrameExporter;
import cineast.core.features.exporter.ShotDescriptorExporter;
import cineast.core.features.exporter.ShotThumbNails;
import cineast.core.features.extractor.Extractor;
import cineast.core.features.extractor.ExtractorInitializer;
import cineast.core.runtime.ImgProvider;
import cineast.core.runtime.QueryDispatcher;
import cineast.core.runtime.ShotDispatcher;
import cineast.core.segmenter.ShotSegmenter;
import cineast.core.util.LogHelper;
import filigrana.FileSysHandler;
import static java.nio.file.CopyOption.*;
import static java.nio.file.StandardCopyOption.*;

public class FeatureExtractionRunner {

	private static final Logger LOGGER = LogManager.getLogger();
	private String absolutePath;
	private String relativePath;
	private File extractionFile;
	private int fileCounter;
	private String targetFolder;
	private String WebRootFolder = "/client/";
	private boolean isWatermark = true;
	private int id;
	private boolean sketch;
	
	public FeatureExtractionRunner(String targetFolder,String extractionFile, boolean Watermark, boolean sketch){
		
		this.extractionFile = new File(extractionFile);
		this.absolutePath = this.extractionFile.getAbsolutePath();

		this.isWatermark = Watermark;
		this.targetFolder = targetFolder;
		this.id = -1;
		this.sketch = sketch;
	}
	
	public FeatureExtractionRunner(String targetFolder,String extractionFile, boolean Watermark, boolean sketch, int id){
		
		this.extractionFile = new File(extractionFile);
		this.absolutePath = this.extractionFile.getAbsolutePath();
		
		this.isWatermark = Watermark;
		this.targetFolder = targetFolder;
		this.id = id;
		this.sketch = sketch;
	}
	
	public int extract(){
		
		if(FileSysHandler.isDirectory(this.extractionFile)){
			//Extract Files in Folder
			
				LinkedList<String> files;
				
				files = FileSysHandler.getFilesOfDirectory(this.extractionFile);
				
				
				
			
				for(String path : files){
					
					
					FeatureExtractionRunner child = new FeatureExtractionRunner(this.targetFolder, path, this.isWatermark, this.sketch, this.id);
					this.fileCounter += child.extract();
					
				}
			
			
			return this.fileCounter;
			
			
		}else{
			//Extract image
			
			//Check if it is an image
			
	        String mimetype= new MimetypesFileTypeMap().getContentType(this.extractionFile);
	        String type = mimetype.split("/")[0];
	       
	        String fileEnding = this.extractionFile.getName().substring(this.extractionFile.getName().lastIndexOf("."));
	        if((type.equals("image") || mimetype.equals("application/octet-stream")) && !fileEnding.equals(".zip") ){

	        	
	        	ADAMWriter watermarkWriter;
	        	if(this.isWatermark){
		        	 watermarkWriter = new ADAMWriter(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword(), "wmk_id"){
		        		//TODO : Check if anonymous class is necessary or cleaner
		    			@Override
		    			public int getParameterCount() {
		    				return 1;
		    			}
	
		    			@Override
		    			public String[] getParameterNames() {
		    				return new String[]{"wmk_name"};
		    			}
		        		
		    		};
		    		watermarkWriter.open("watermark");
	        	}else{
	        		 watermarkWriter = new ADAMWriter(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword(), "iph_id"){
		        		//TODO : Check if anonymous class is necessary or cleaner
		    			@Override
		    			public int getParameterCount() {
		    				return 1;
		    			}
	
		    			@Override
		    			public String[] getParameterNames() {
		    				return new String[]{"iph_name"};
		    			}
		    			
		    		};
		    		watermarkWriter.open("iph_type");
	
	        	}
	    		int wmk_id;

	    		
	    		String md5 = "1";
				try {
					FileInputStream fis = new FileInputStream(this.extractionFile);
					md5 = org.apache.commons.codec.digest.DigestUtils.md5Hex(fis);
					fis.close();
				} catch (FileNotFoundException e2) {
					// TODO Auto-generated catch block
					LOGGER.debug(LogHelper.getStackTrace(e2));
				} catch (IOException e3) {
					// TODO Auto-generated catch block
					LOGGER.debug(LogHelper.getStackTrace(e3));
				}
	    		
	    		if(watermarkWriter.check("select * from picture where pic_md5 = \'" + md5 + "\'")){
	    			//If md5 already in Database
	    			LOGGER.info(this.absolutePath + " md5 allready in database");
	    			return 0;
	    		}else{
	    			
	    			String targetPath = this.targetFolder + "/" + System.currentTimeMillis() + fileEnding; 
	 
	    			Boolean copied = false;
	    				try {
	    					
	    					Files.copy(this.extractionFile, new File(targetPath));
							
							copied = true;	
	    				} catch (IOException e2) {
							// TODO Auto-generated catch block
							LOGGER.debug(LogHelper.getStackTrace(e2));
						}
	    				
	    			if(copied){
	    				this.extractionFile = new File(targetPath);
	    				this.absolutePath = this.extractionFile.getAbsolutePath();
	    				int index = this.absolutePath.indexOf(this.WebRootFolder);
	    				this.relativePath = this.absolutePath.substring(index+this.WebRootFolder.length());
		    			
		    			ReturningADAMTuple tuple = (ReturningADAMTuple) watermarkWriter.makeTuple("");
		    		
		    			if(this.id == -1){
			    			watermarkWriter.write(tuple);
			    			wmk_id = (int) tuple.getReturnValue();
			    			
		    			}else{
		    				wmk_id = this.id;
		    			}	
		    			ADAMWriter pictureWriter;
		    			if(this.isWatermark){
			    		     pictureWriter = new ADAMWriter(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword(), "pic_id"){
			            		//TODO : Check if anonymous class is necessary or cleaner
			        			@Override
			        			public int getParameterCount() {
			        				return 4;
			        			}
		
			        			@Override
			        			public String[] getParameterNames() {
			        				return new String[]{"pic_path","pic_md5", "pic_sketch", "pic_wmk_id"};
			        			}
			        			
			        		};
		    			}else{
		    				   pictureWriter = new ADAMWriter(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword(), "pic_id"){
				            		//TODO : Check if anonymous class is necessary or cleaner
				        			@Override
				        			public int getParameterCount() {
				        				return 3;
				        			}
			
				        			@Override
				        			public String[] getParameterNames() {
				        				return new String[]{"pic_path","pic_md5", "pic_sketch", "pic_iph_id"};
				        			}
				        			
				        		};
		    			}
		        		pictureWriter.open("picture");
		        		tuple = (ReturningADAMTuple) pictureWriter.makeTuple(this.relativePath, md5,this.sketch, wmk_id );
		        		pictureWriter.write(tuple);
		    			int pic_id = (int)tuple.getReturnValue();
		    			
		    			ArrayList<Extractor> featureList = API.getExtractors();
		    			
		    			
		    			ExtractorInitializer initializer = new ExtractorInitializer() {
		    				
		    				@Override
		    				public void initialize(Extractor e) {
		    					e.init(new ADAMWriter(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword()){

		    						@Override
		    						public int getParameterCount() {
		    							return 0;
		    						}

		    						@Override
		    						public String[] getParameterNames() {
		    							return null;
		    						}
		    						
		    					});				
		    				}
		    			};
		    			
		    			try {
		    	
							BufferedImage imgbuf = ImageIO.read(this.extractionFile);
						
							MultiImage img = MultiImageFactory.newMultiImage(imgbuf, pic_id);
							
							img = EdgeImg.turnTransparentIntoWhite(img);

							ImgProvider imgprov = new ImgProvider(pic_id,img); 
							
							ShotDispatcher dispatcher = new ShotDispatcher(featureList, initializer, imgprov);
							
							dispatcher.run();
							
							return 1;
						} catch (IOException e1) {
							// TODO Auto-generated catch block
							LOGGER.error(e1.getStackTrace());
							return 0;
						}
		    			
		    			
	    			}else{
	    				LOGGER.error("File Moving not worked from {} to {}", this.absolutePath, targetPath);
	    				return 0;
	    			}
	    			
	    			
	    		}
	        	
	        	
	        }else{
	        	LOGGER.info("File was no image: {}", this.absolutePath);
	        	return 0;
	        }
	        
	    
    		
    		
			
			
			
		}
		
		
		
		
		
		
		
		

		
		

		
		
		

		
		
		
		
	}
	
}
