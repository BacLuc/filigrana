package cineast.core.features.retriever;

import java.util.List;

import cineast.core.data.FrameContainer;
import cineast.core.data.LongDoublePair;
import cineast.core.data.QueryContainer;
import cineast.core.db.DBSelector;

public interface Retriever {

	void init(DBSelector selector);
	
	List<LongDoublePair> getSimilar(FrameContainer qc);
	
	List<LongDoublePair> getSimilar(long shotId);

	void finish();
}
