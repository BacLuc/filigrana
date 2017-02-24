package cineast.core.config;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Properties;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.util.LogHelper;
import cineast.core.features.abstracts.AbstractFeatureModule;
import cineast.core.features.filigranafeatures.*;


public class Config {

	private Config(){}
	
	private static Properties properties = new Properties();
	private static final Logger LOGGER = LogManager.getLogger();
	
	static{
		try{
			FileInputStream fin = new FileInputStream("cineast.properties");
			properties.load(fin);
			fin.close();
			LOGGER.info("prpoerties loaded");
		}catch(FileNotFoundException e){
			LOGGER.warn("properties file not found");
		} catch (IOException e) {
			LOGGER.warn("could not read properties file");
		}
	}
	
	public static String getDBLocation(){
		return properties.getProperty("database", "filigrana.cloudapp.net:5433/filigrana");
	}
	
	public static String getDBUser(){
		return properties.getProperty("user", "postgres");
	}
	
	public static String getDBPassword(){
		return properties.getProperty("pass", "semantic71Wein");
	}
	
	public static int numbetOfPoolThreads(){
		String threads = properties.getProperty("numbetOfPoolThreads", "6");
		try{
			return Integer.parseInt(threads);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 6;
	}
	
	public static int resultsPerModule(){
		String threads = properties.getProperty("resultsPerModule", "40");
		try{
			return Integer.parseInt(threads);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 40;
	}
	
	public static int maxResults(){
		String threads = properties.getProperty("maxResults", "20");
		try{
			return Integer.parseInt(threads);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 50;
	}
	
	public static File frameCacheFolder(){
		String path = properties.getProperty("frameCacheFolder", ".");
		File folder = new File(path);
		if((folder.exists() && folder.isDirectory()) || folder.mkdirs()){
			return folder;
		}else{
			LOGGER.error("frame cache path {} does not exist, using default: .", folder.getAbsolutePath());
			return new File(".");
		}
	}

	public static int shotQueueSize() {
		String threads = properties.getProperty("shotQueueSize", "5");
		try{
			return Integer.parseInt(threads);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 5;
	}
	
	
	public static HashMap<AbstractFeatureModule, Double> getUsedFeatures(){
		HashMap<AbstractFeatureModule, Double> returnList = new HashMap<AbstractFeatureModule, Double>();
		returnList.put(new EdgeARP88(),					0.85);
		returnList.put(new EdgeGrid16(),				1.15);
		returnList.put(new EdgeARP88Full(),				0.85);
		returnList.put(new EdgeGrid16Full(),			0.85);
		returnList.put(new EHD(),						0.7);
		returnList.put(new DominantEdgeGrid16(),		1.4);
		returnList.put(new DominantEdgeGrid8(),			1.4);
		
		//returnList.put(new QueryImageExporter(), 1d);
		
		
		
		return returnList;
	}
	
	public static String getServerRoot(){
		String root = properties.getProperty("ServerRoot", "/mnt/Windows7_OS/wamp/www2/Filigrana/filigrana/web/server");
		
		
		return root;
	}
	
	public static boolean getWithSketch(){
	
			String withSketch = properties.getProperty("withSketch", "true");
			try{
				return Boolean.parseBoolean(withSketch);
			}catch(Exception e){
				LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
			}
			return false;
		
	}
	
	public static int getServerPort(){
		String portstring = properties.getProperty("port", "12345");
		try{
			return Integer.parseInt(portstring);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 12345;
	}
	
	public static int getTimeForCleanup(){
		String portstring = properties.getProperty("cleanUpTime", "2");
		try{
			return Integer.parseInt(portstring);
		}catch(Exception e){
			LOGGER.warn("error while parsing properties: {}", LogHelper.getStackTrace(e));
		}
		return 12345;
	}
	
}
