package cineast.core.features.abstracts;

import cineast.core.data.FeatureString;
import cineast.core.db.DBSelector;
import cineast.core.db.PersistencyWriter;
import cineast.core.db.PersistentTuple;
import cineast.core.features.extractor.Extractor;
import cineast.core.features.retriever.Retriever;

public abstract class AbstractFeatureModule implements Extractor, Retriever {

	protected PersistencyWriter phandler;
	protected DBSelector selector;

	@Override
	public void init(DBSelector selector) {
		this.selector = selector;
	}

	protected void addToDB(long pic_id, FeatureString fs) {
		PersistentTuple tuple = this.phandler.makeTuple(pic_id, fs);
		this.phandler.write(tuple);
	}

	@Override
	public void finish() {
		if (this.phandler != null) {
			this.phandler.close();
		}
		if (this.selector != null) {
			this.selector.close();
		}
	}
	
	
	
	
	

}
