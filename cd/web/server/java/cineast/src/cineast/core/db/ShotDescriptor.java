package cineast.core.db;
import java.sql.ResultSet;
import java.sql.SQLException;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.util.LogHelper;

public class ShotDescriptor{
		private static final Logger LOGGER = LogManager.getLogger();
		private long shotId;
		private int videoId = -1, width = -1, height = -1, framecount = -1, shotNumber = -1, startFrame = -1, endFrame = -1;
		private float seconds = -1f;
		private String name = null, path = null;
		private long picid= -1;
		
		ShotDescriptor(long id){
			this.shotId=id;
		}
		
		ShotDescriptor(long id, long picid){
			this.shotId = id;
			this.picid = picid;
		}
		
		ShotDescriptor(ResultSet rset, long shotId){
			this.shotId = shotId;
			if(rset != null){
				try {
					rset.next();
					this.videoId	= rset.getInt(1);
					this.name	= rset.getString(2);
					this.path	= rset.getString(3);
					this.width	= rset.getInt(4);
					this.height	= rset.getInt(5);
					this.framecount	= rset.getInt(6);
					this.seconds = rset.getFloat(7);
					this.shotNumber = rset.getInt(9);
					this.startFrame = rset.getInt(11);
					this.endFrame = rset.getInt(12);
				} catch (SQLException e) {
					LOGGER.warn(LogHelper.SQL_MARKER, LogHelper.getStackTrace(e));
				}
			}
		}
		
		

		public long getShotId() {
			return shotId;
		}
		
		public long getPicId() {
			return this.picid;
		}

		public int getVideoId() {
			return videoId;
		}

		public int getWidth() {
			return width;
		}

		public int getHeight() {
			return height;
		}

		public int getFramecount() {
			return framecount;
		}

		public int getShotNumber() {
			return shotNumber;
		}

		public int getStartFrame() {
			return startFrame;
		}

		public int getEndFrame() {
			return endFrame;
		}
		
		public float getSeconds() {
			return seconds;
		}

		public String getName() {
			return name;
		}

		public String getPath() {
			return path;
		}
		
		public float getFPS(){
			return framecount / seconds;
		}

		@Override
		public String toString() {
			return "ShotDescriptor(" + shotId + ")";
		}

	}
