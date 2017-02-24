package cineast.core.features.filigranafeatures;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import cineast.core.config.Config;
import cineast.core.data.FloatVector;
import cineast.core.data.FrameContainer;
import cineast.core.data.LongDoublePair;
import cineast.core.db.PersistencyWriter;
import cineast.core.features.abstracts.AbstractFeatureModule;

public abstract class AbstractFiligranaFeatureModule extends AbstractFeatureModule {
	protected String tablename = "";
	protected String idField = "fet_pic_id";
	protected int limit = Config.resultsPerModule();

	@Override
	public void init(PersistencyWriter<?> phandler) {
		this.phandler = phandler;
		this.phandler.open(this.tablename);
	}

	private void setTablename(String tablename){
		this.tablename=tablename;
	}
	private String getTablename(){
		return this.tablename;
	}

	public Boolean checkIfNotExtracted(long pic_id){
		if (!phandler.check("SELECT * FROM "+this.tablename+" WHERE "+this.idField+" = " + pic_id)){
			return true;
		}else{
			return false;
		}
	}
	
	public ResultSet getSimilar(String tablename, FloatVector fv, String type, boolean wm){
		String sql = "";
		if(wm){
			 sql = 
				"SELECT * FROM "
				+ "(SELECT "+tablename+".* "
						+ "FROM "+tablename+", picture "
						+ "WHERE fet_pic_id = pic_id AND ";
			if(!Config.getWithSketch()){
				sql += " pic_sketch = false AND ";
			}
			sql+=			 "pic_iph_id IS NULL) t   USING DISTANCE MINKOWSKI(2)(\'" + fv.toFeatureString() + "\', "+type+") ORDER USING DISTANCE LIMIT " + limit;
		}else{
			 sql = 
				"SELECT * FROM "
				+ "(SELECT "+tablename+".* "
						+ "FROM "+tablename+", picture "
						+ "WHERE fet_pic_id = pic_id AND ";
			if(!Config.getWithSketch()){
				sql += " pic_sketch = false AND ";
			}
			sql+="pic_wmk_id IS NULL) t   USING DISTANCE MINKOWSKI(2)(\'" + fv.toFeatureString() + "\', "+type+") ORDER USING DISTANCE LIMIT " + limit;
		}
		
			ResultSet rset = this.selector.select(sql);
		
		return rset;
	}
	@Override
	public List<LongDoublePair> getSimilar(long id){
		return null;
	}





	@Override
	public void processShot(FrameContainer shot) {
		// TODO Auto-generated method stub
		
	}





	@Override
	public List<LongDoublePair> getSimilar(FrameContainer qc) {
		// TODO Auto-generated method stub
		return null;
	}

}
