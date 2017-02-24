package cineast.api;

import gnu.trove.map.hash.TObjectDoubleHashMap;

import java.io.IOException;
import java.net.ServerSocket;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Set;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.config.Config;
import cineast.core.db.DBSelector;
import cineast.core.features.DominantEdgeGrid16;
import cineast.core.features.DominantEdgeGrid8;
import cineast.core.features.EHD;
import cineast.core.features.EdgeARP88;
import cineast.core.features.EdgeARP88Full;
import cineast.core.features.EdgeGrid16;
import cineast.core.features.EdgeGrid16Full;
import cineast.core.features.abstracts.AbstractFeatureModule;
import cineast.core.features.exporter.QueryImageExporter;
import cineast.core.features.extractor.Extractor;
import cineast.core.features.retriever.Retriever;
import cineast.core.features.retriever.RetrieverInitializer;
import cineast.core.run.CleanupThread;
import cineast.core.util.LogHelper;

public class API {

	private static RetrieverInitializer initializer = new RetrieverInitializer() {
		//TODO : Look that no anonymous class is used
		@Override
		public void initialize(Retriever r) {
			r.init(new DBSelector());

		}
	};

	private static int port = Config.getServerPort();
	private static Logger LOGGER = LogManager.getLogger();

	public static void main(String[] args) {
		// TODO parse settings

		try {
			ServerSocket ssocket = new ServerSocket(port);
			CleanupThread cleanup = new CleanupThread();
			while (true) {
				if(!cleanup.isAlive()){
					cleanup.start();
				}	
				JSONAPIThread thread = new JSONAPIThread(ssocket.accept());
				thread.start();
				
				
				
			}
		} catch (IOException e) {
			LOGGER.fatal(LogHelper.getStackTrace(e));
		}

	}

	

	public static HashMap<Retriever, Double> getRetrievers() {

		HashMap<AbstractFeatureModule, Double> featureList =Config.getUsedFeatures();
		
		HashMap<Retriever, Double> retrievers = new HashMap<Retriever, Double>();
		
		Set<AbstractFeatureModule> keyList = featureList.keySet();
		
		for(AbstractFeatureModule key : keyList){
			retrievers.put((Retriever) key, featureList.get(key));
			
		}
		return retrievers;
	}
	
	public static ArrayList<Extractor> getExtractors() {

		HashMap<AbstractFeatureModule, Double> featureList =Config.getUsedFeatures();
		
		ArrayList<Extractor> extractors = new ArrayList<Extractor>();
		
		Set<AbstractFeatureModule> keyList = featureList.keySet();
		
		for(AbstractFeatureModule key : keyList){
			extractors.add((Extractor) key);
			
		}
		
		return extractors;
		
	}

	public static RetrieverInitializer getInitializer() {
		return initializer;
	}

	

}

