package cineast.core.run;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.joda.time.DateTime;

import cineast.core.config.Config;
import cineast.core.db.DBSelector;
import cineast.core.util.LogHelper;

public class CleanupThread extends Thread {
	private static final Logger LOGGER = LogManager.getLogger();
	public void run(){
		DBSelector selector = new DBSelector();
		
		DateTime now = DateTime.now();
		
		DateTime targetTime = DateTime.now();
		targetTime = targetTime.withHourOfDay(Config.getTimeForCleanup());
		targetTime = targetTime.plusDays(1);
		
		long milisecondsToSleep = targetTime.toDate().getTime() - now.toDate().getTime();
		
		try {
			Thread.sleep(milisecondsToSleep);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			LOGGER.error(LogHelper.getStackTrace(e));
		}
	
		while(true){
			LOGGER.debug("resetting indices");
				if(selector.executeFunction("select reset_indices();")){
					LOGGER.debug("indices resetted");
				}else{
					LOGGER.error("indices resetting error");
				}
			try {
				Thread.sleep(24*60*60*1000);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				LOGGER.error(LogHelper.getStackTrace(e));
			}
			
			
		}
	}

}
