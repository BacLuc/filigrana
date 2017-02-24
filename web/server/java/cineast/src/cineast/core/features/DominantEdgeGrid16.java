package cineast.core.features;

import georegression.struct.point.Point2D_I32;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import boofcv.alg.feature.detect.edge.EdgeContour;
import boofcv.alg.feature.detect.edge.EdgeSegment;
import cineast.core.config.Config;
import cineast.core.data.FloatVector;
import cineast.core.data.FloatVectorImpl;
import cineast.core.data.FrameContainer;
import cineast.core.data.LongDoublePair;
import cineast.core.data.MultiImage;
import cineast.core.db.PersistencyWriter;
import cineast.core.descriptor.EdgeList;
import cineast.core.features.abstracts.AbstractFeatureModule;
import cineast.core.util.LogHelper;
import cineast.core.util.MathHelper;

public class DominantEdgeGrid16 extends AbstractFeatureModule {

	private static final Logger LOGGER = LogManager.getLogger();
	private static final double MAX_DIST = 16963f * 2;
	
	@Override
	public void init(PersistencyWriter<?> phandler) {
		this.phandler = phandler;
		this.phandler.open("features.DominantEdgeGrid16");
	}

	@Override
	public void processShot(FrameContainer shot) {
		LOGGER.entry();
		if (!phandler.check("SELECT * FROM features.DominantEdgeGrid16 WHERE shotid = " + shot.getId())) {
			short[][][] edgeHist = new short[16][16][4];
			buildEdgeHist(edgeHist, shot.getMostRepresentativeFrame().getImage());
			short[] dominant = getDominants(edgeHist);
			FloatVector fv = new FloatVectorImpl(dominant);
			addToDB(shot.getId(), fv);
		}
		LOGGER.exit();
	}

	@Override
	public List<LongDoublePair> getSimilar(FrameContainer qc) {
		int limit = Config.resultsPerModule();
		short[][][] edgeHist = new short[16][16][4];
		buildEdgeHist(edgeHist, qc.getMostRepresentativeFrame().getImage());
		short[] dominant = getDominants(edgeHist);
		FloatVector fv = new FloatVectorImpl(dominant);
		ResultSet rset = this.selector.select("SELECT * FROM features.DominantEdgeGrid16 USING DISTANCE MINKOWSKI(2)(\'" + fv.toFeatureString() + "\', edges) ORDER USING DISTANCE LIMIT " + limit);
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

	@Override
	public List<LongDoublePair> getSimilar(long shotId) {
		int limit = Config.resultsPerModule();		
		ResultSet rset = this.selector.select("WITH q AS (SELECT edges FROM features.DominantEdgeGrid16 WHERE shotid = " + shotId + ") SELECT shotid FROM features.DominantEdgeGrid16, q USING DISTANCE MINKOWSKI(2)(q.edges, DominantEdgeGrid16.edges) ORDER USING DISTANCE LIMIT " + limit);
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

	static void buildEdgeHist(short[][][] edgeHist, MultiImage img){
		List<EdgeContour> contourList = EdgeList.getEdgeList(img);
		for(EdgeContour contour : contourList){
			for(EdgeSegment segment : contour.segments){
				List<Point2D_I32> points = segment.points;
				if(points.size() >= 2){
					Iterator<Point2D_I32> iter = points.iterator();
					Point2D_I32 last = iter.next();
					while (iter.hasNext()) {
						Point2D_I32 current = iter.next();
						int dX = current.x - last.x, dY = current.y - last.y;
						if(dX != 0 || dY != 0){
							int cX = (current.x + last.x) / 2, cY = (current.y + last.y) / 2;
							edgeHist[(int)Math.floor(cX / (img.getWidth() / 15.9999f))][(int)Math.floor(cY / (img.getHeight() / 15.9999f))][(int)(((Math.atan2(dY, dX) + Math.PI) / Math.PI) * 4 % 4)]++;
							
						}
						last = current;
					}
				}
			}
		}
	}
	
	static short[] getDominants(short[][][] edgeHist){
		short[] dominant = new short[16 * 16];
		for(int y = 0; y < 16; ++y){
			for(int x = 0; x < 16; ++x){
				short idx = -10; 
				int max = 0;
				for(short i = 0; i < 4; ++i){
					if(edgeHist[y][x][i] > max){
						idx = i;
						max = edgeHist[y][x][i];
					}
				}
				dominant[16 * y + x] = idx;
			}
		}
		return dominant;
	}
}
