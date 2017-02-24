package cineast.core.db;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import java.util.Properties;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.config.Config;
import cineast.core.db.ShotDescriptor;
import cineast.core.util.LogHelper;

public class FiligranaLookup implements Lookup {
	private static final Logger LOGGER = LogManager.getLogger();
	
	private Connection connection;
	private boolean isWatermark;
	
	
	public FiligranaLookup(){
		this(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword());
	}
	public FiligranaLookup(String database, String username, String password){
		Properties props = new Properties();

		props.setProperty("user", username);
		props.setProperty("password", password);
		props.setProperty("tcpKeepAlive", "true");

		String url = "jdbc:postgresql://" + database;
		try {
			connection = DriverManager.getConnection(url, props);
		} catch (SQLException e) {
			LOGGER.fatal(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
	}
	
	public void setWatermark(boolean wm){
		this.isWatermark = wm;
	}
	
	@Override
	public void close() {
		try {
			this.connection.close();
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
		
	}

	@Override
	public ShotDescriptor lookUpShot(long shotId){
		ResultSet rset = null;
		try {
			PreparedStatement statement;
			String sql = "";
			if(this.isWatermark){
				 sql+="SELECT pic_wmk_id FROM picture WHERE pic_iph_id IS null AND pic_id = " + shotId;
			}else{
				 sql +="SELECT pic_iph_id FROM picture WHERE pic_wmk_id IS null AND pic_id = " + shotId;
			}
			if(!Config.getWithSketch()){
				sql+= " AND pic_sketch = false";
			}
			LOGGER.debug(sql);
			statement = connection.prepareStatement(sql);
			rset = statement.executeQuery();
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
		long id=-1;
		if(rset != null){
			try {
				rset.next();
				id	= rset.getInt(1);
				
				return new ShotDescriptor(id, shotId);
			} catch (SQLException e) {
				LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
			}
		}
		return null;
			
		
	}

	@Override
	public int lookUpVideoid(String name) {
		// TODO Auto-generated method stub
		return 0;
	}

	@Override
	public List<ShotDescriptor> lookUpVideo(int videoId) {
		// TODO Auto-generated method stub
		return null;
	}
	
	public void finalize()throws Throwable {
		this.connection.close();
		super.finalize();
	}

}
