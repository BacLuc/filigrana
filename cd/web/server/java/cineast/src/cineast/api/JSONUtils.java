package cineast.api;

import georegression.struct.point.Point2D_F32;
import gnu.trove.map.hash.TObjectDoubleHashMap;

import java.awt.image.BufferedImage;
import java.io.IOException;
import java.io.Reader;
import java.util.LinkedList;
import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.data.InMemoryMultiImage;
import cineast.core.data.LongDoublePair;
import cineast.core.data.MultiImageFactory;
import cineast.core.data.Pair;
import cineast.core.data.QueryContainer;
import cineast.core.data.QuerySubTitleItem;
import cineast.core.db.Lookup;
import cineast.core.db.ShotDescriptor;
import cineast.core.db.ShotLookup;
import cineast.core.util.LogHelper;

import com.eclipsesource.json.JsonArray;
import com.eclipsesource.json.JsonObject;
import com.eclipsesource.json.JsonValue;

public class JSONUtils {

	private static Logger LOGGER = LogManager.getLogger();
	private JSONUtils(){}
	
	public static Pair<QueryContainer, TObjectDoubleHashMap<String>> readQueryFromJSON(Reader reader){
		LOGGER.entry();
		try {
			JsonObject jobj_in = JsonObject.readFrom(reader);
			BufferedImage img = WebUtils.dataURLtoBufferedImage(jobj_in.get("img").asString());
			QueryContainer qc = new QueryContainer(MultiImageFactory.newInMemoryMultiImage(img));
			JsonArray subs = jobj_in.get("subelements").asArray();
			for(JsonValue jv : subs){
				qc.getSubtitleItems().add(new QuerySubTitleItem(jv.asString()));
			}
			JsonArray motion = jobj_in.get("motion").asArray();
			for(JsonValue motionPath : motion){
				LinkedList<Point2D_F32> pathList = new LinkedList<Point2D_F32>();
				for(JsonValue point : motionPath.asArray()){
					JsonArray pa = point.asArray();
					pathList.add(new Point2D_F32(pa.get(0).asFloat(), pa.get(1).asFloat()));
				}
				qc.getPaths().add(pathList);
			}
			qc.setRelativeStart(jobj_in.get("start").asFloat());
			qc.setRelativeStart(jobj_in.get("end").asFloat());
			
			String weights = jobj_in.get("weights").toString();
			
			TObjectDoubleHashMap<String> weightMap = getWeightsFromJsonString(weights);
			
			return LOGGER.exit(new Pair<QueryContainer, TObjectDoubleHashMap<String>>(qc, weightMap));
		} catch (IOException e) {
			LOGGER.error(LogHelper.getStackTrace(e));
			return null;
		}
	}
	
	
	
	public static JsonArray resultToJSONArray(List<LongDoublePair> result, Lookup sl){
		JsonArray jarr = new JsonArray();
		for(LongDoublePair p : result){
			long shotId = p.key;
			ShotDescriptor descriptor = sl.lookUpShot(shotId);
			if(descriptor != null){
				JsonObject jobj = new JsonObject();
				jobj.add("score", p.value).add("name", descriptor.getName());
				jobj.add("startframe", descriptor.getStartFrame()).add("endframe", descriptor.getEndFrame());
				jobj.add("shotid", descriptor.getShotId());
				jobj.add("videoid", descriptor.getVideoId());
				jobj.add("framerate", descriptor.getFPS());
				jobj.add("path", descriptor.getPath());
				jobj.add("picid", descriptor.getPicId());
				jarr.add(jobj);
			}
		}
		return jarr;
	}
	
	
	
	public static String resultToJSONString(List<LongDoublePair> result, Lookup sl){
		return resultToJSONArray(result, sl).toString();
	}
	
	public static TObjectDoubleHashMap<String> getWeightsFromJsonString(String s){
		TObjectDoubleHashMap<String> weightMap = new TObjectDoubleHashMap<String>();
		JsonObject weights = JsonObject.readFrom(s);
		weightMap.put("global", weights.get("global").asDouble());
		weightMap.put("local", weights.get("local").asDouble());
		weightMap.put("edge", weights.get("edge").asDouble());
		weightMap.put("text", weights.get("text").asDouble());
		weightMap.put("motion", weights.get("motion").asDouble());
		weightMap.put("complex", weights.get("complex").asDouble());
		return weightMap;
	}
	
}
