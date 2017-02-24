package cineast.core.runtime;

import java.util.List;
import java.util.concurrent.Callable;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.LongDoublePair;
import cineast.core.data.Pair;
import cineast.core.data.QueryContainer;
import cineast.core.features.retriever.Retriever;

public class RetrievalTask implements Callable<Pair<Retriever, List<LongDoublePair>>> {

	private Retriever retriever;
	private QueryContainer query = null;
	private long shotId = -1;
	private static final Logger LOGGER = LogManager.getLogger();
		
	private RetrievalTask(Retriever retriever){
		this.retriever = retriever;
	}
	
	RetrievalTask(Retriever retriever, QueryContainer query) {
		this(retriever);
		this.query = query;
	}
	
	RetrievalTask(Retriever retriever, long shotId) {
		this(retriever);
		this.shotId = shotId;
	} 
	
	@Override
	public Pair<Retriever, List<LongDoublePair>> call() throws Exception {
		LOGGER.entry();
		LOGGER.debug("starting {}", retriever.getClass().getSimpleName());
		List<LongDoublePair> result;
		if(this.query == null){
			result = this.retriever.getSimilar(this.shotId);
		}else{
			result = this.retriever.getSimilar(this.query);
		}
		return LOGGER.exit(new Pair<Retriever, List<LongDoublePair>>(this.retriever, result));
	}


}