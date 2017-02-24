package cineast.core.features.retriever;

import gnu.trove.map.hash.TLongObjectHashMap;

import java.util.HashMap;
import java.util.List;

import cineast.core.data.FrameContainer;
import cineast.core.data.LongDoublePair;
import cineast.core.data.QueryContainer;
import cineast.core.db.DBSelector;

public class CachedRetriever implements Retriever {

	private Retriever retriever;
	private HashMap<FrameContainer, List<LongDoublePair>> resultCache = new HashMap<FrameContainer, List<LongDoublePair>>();
	private TLongObjectHashMap<List<LongDoublePair>> longResultCache = new TLongObjectHashMap<List<LongDoublePair>>();
	private DBSelector selector = null;
	
	public CachedRetriever(Retriever retriever){
		this.retriever = retriever;
	}
	
	@Override
	public void init(DBSelector selector) {
		this.selector = selector;
	}

	@Override
	public List<LongDoublePair> getSimilar(FrameContainer qc) {
		if(this.resultCache.containsKey(qc)){
			return this.resultCache.get(qc);
		}
		this.retriever.init(this.selector);
		List<LongDoublePair> result = this.retriever.getSimilar(qc);
		this.resultCache.put(qc, result);
		return result;
	}

	@Override
	public List<LongDoublePair> getSimilar(long shotId) {
		if(this.longResultCache.containsKey(shotId)){
			return this.longResultCache.get(shotId);
		}
		this.retriever.init(this.selector);
		List<LongDoublePair> result = this.retriever.getSimilar(shotId);
		this.longResultCache.put(shotId, result);
		return result;
	}

	@Override
	public void finish() {
		this.retriever.finish();
	}
	
	public Retriever getRetriever(){
		return this.retriever;
	}

}
