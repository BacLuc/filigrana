package cineast.core.features;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.config.Config;
import cineast.core.data.FloatVector;
import cineast.core.data.FloatVectorImpl;
import cineast.core.data.FrameContainer;
import cineast.core.data.LongDoublePair;
import cineast.core.data.MultiImage;
import cineast.core.data.StatElement;
import cineast.core.db.PersistencyWriter;
import cineast.core.descriptor.EdgeImg;
import cineast.core.features.abstracts.AbstractFeatureModule;
import cineast.core.util.ARPartioner;
import cineast.core.util.LogHelper;
import cineast.core.util.MathHelper;

public class EdgeARP88 extends AbstractFeatureModule {

	private static final Logger LOGGER = LogManager.getLogger();
	private static final float MAX_DIST = (float) (2f * MathHelper.SQRT2);
	
	@Override
	public void init(PersistencyWriter<?> phandler) {
		this.phandler = phandler;
		this.phandler.open("features.EdgeARP88");

	}

	@Override
	public void processShot(FrameContainer shot) {
		LOGGER.entry();
		if (!phandler.check("SELECT * FROM features.EdgeARP88 WHERE shotid = " + shot.getId())) {
			addToDB(shot.getId(), getEdges(shot.getMostRepresentativeFrame().getImage()));
		}
		LOGGER.exit();
	}

	@Override
	public List<LongDoublePair> getSimilar(FrameContainer qc) {

		int limit = Config.resultsPerModule();
		FloatVector query = getEdges(qc.getMostRepresentativeFrame().getImage());
		
		ResultSet rset = this.selector.select("SELECT * FROM features.EdgeARP88 USING DISTANCE MINKOWSKI(2)(\'" + query.toFeatureString() + "\', arp) ORDER USING DISTANCE LIMIT " + limit);
		ArrayList<LongDoublePair> result = new ArrayList<>(limit);
		if(rset != null){
			try {
				while(rset.next()){
					double dist = rset.getDouble(1);
					long shotId = rset.getLong(2);
					
					result.add(new LongDoublePair(shotId, MathHelper.getScore(dist, MAX_DIST)));
				}
			} catch (SQLException e) {
				LOGGER.fatal(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
			}
		}
		
		return result;
	}

	private static FloatVector getEdges(MultiImage img){
		StatElement[] stats = new StatElement[64];
		for(int i = 0; i < 64; ++i){
			stats[i] = new StatElement();
		}
		List<Boolean> edgePixels = EdgeImg.getEdgePixels(img, new ArrayList<Boolean>(img.getWidth() * img.getHeight()));
		ArrayList<LinkedList<Boolean>> partition = ARPartioner.partition(edgePixels, img.getWidth(), img.getHeight(), 8, 8);
		for(int i = 0; i < partition.size(); ++i){
			LinkedList<Boolean> edge = partition.get(i);
			StatElement stat = stats[i];
			for(boolean b : edge){
				stat.add(b ? 1 : 0);
			}
		}
		float[] f = new float[64];
		for(int i = 0; i < 64; ++i){
			f[i] = stats[i].getAvg();
		}
		
		return new FloatVectorImpl(f);
	}

	@Override
	public List<LongDoublePair> getSimilar(long shotId) {
		int limit = Config.resultsPerModule();		
		ResultSet rset = this.selector.select("WITH q AS (SELECT arp FROM features.EdgeARP88 WHERE shotid = " + shotId + ") SELECT shotid FROM features.EdgeARP88, q USING DISTANCE MINKOWSKI(2)(q.arp, EdgeARP88.arp) ORDER USING DISTANCE LIMIT " + limit);
		ArrayList<LongDoublePair> result = new ArrayList<>(limit);
		if(rset != null){
			try {
				while(rset.next()){
					double dist = rset.getDouble(1);
					long id = rset.getLong(2);

					result.add(new LongDoublePair(id, MathHelper.getScore(dist, MAX_DIST)));
				}
			} catch (SQLException e) {
				LOGGER.fatal(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
			}
		}
		
		return result;
	}
	
}
