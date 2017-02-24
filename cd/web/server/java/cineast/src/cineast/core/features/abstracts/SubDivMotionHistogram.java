package cineast.core.features.abstracts;

import cineast.core.data.FeatureString;
import cineast.core.db.PersistencyWriter;
import cineast.core.db.PersistentTuple;
import cineast.core.features.extractor.Extractor;

public abstract class SubDivMotionHistogram extends MotionHistogramCalculator implements Extractor {

	protected PersistencyWriter phandler;
	
	protected SubDivMotionHistogram(){}

	protected void addToDB(long shotId, FeatureString fs1, FeatureString fs2) {
		PersistentTuple tuple = this.phandler.makeTuple(shotId, fs1, fs2);
		this.phandler.write(tuple);
	}
	
	@Override
	public void finish() {
		if(this.phandler != null){
			this.phandler.close();
		}
		super.finish();
	}
}
