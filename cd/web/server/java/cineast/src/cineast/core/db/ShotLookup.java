package cineast.core.db;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.LinkedList;
import java.util.List;
import java.util.Properties;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.config.Config;
import cineast.core.util.LogHelper;

public class ShotLookup implements Lookup{

	private static final Logger LOGGER = LogManager.getLogger();
	
	private Connection connection;
	
	public ShotLookup(){
		this(Config.getDBLocation(), Config.getDBUser(), Config.getDBPassword());
	}
	
	public ShotLookup(String database, String username, String password){
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
	
	public void close(){
		try {
			this.connection.close();
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
	}
	
	public ShotDescriptor lookUpShot(long shotId){
		ResultSet set = null;
		try {
			PreparedStatement statement = connection.prepareStatement("SELECT * FROM cineast.videos JOIN cineast.shots ON (cineast.videos.id = cineast.shots.video) WHERE cineast.shots.id = " + shotId);
			set = statement.executeQuery();
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
		
		return new ShotDescriptor(set, shotId);
		
	}
	
	
	public int lookUpVideoid(String name){
		
		try {
			PreparedStatement statement = connection.prepareStatement("SELECT id FROM cineast.videos WHERE name = \'" + name + "\'");
			ResultSet set = statement.executeQuery();
			if(set.next()){
				return set.getInt(1);
			}
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
		return -1;

	}
	
	public List<ShotDescriptor> lookUpVideo(int videoId){
		LinkedList<ShotDescriptor> _return = new LinkedList<ShotDescriptor>();
		try {
			PreparedStatement statement = connection.prepareStatement("SELECT id FROM cineast.shots WHERE video = " + videoId);
			ResultSet set = statement.executeQuery();
			while(set.next()){
				long shotId = set.getLong(1);
				ShotDescriptor des = lookUpShot(shotId);
				if(des.getVideoId() == videoId){//sanity check
					_return.add(des);
				}
			}
		} catch (SQLException e) {
			LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
		}
		
		return _return;
	}
	
	@Override
	public void finalize() throws Throwable {
		this.connection.close();
		super.finalize();
	}

	
	
}
