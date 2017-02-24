package test;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.joda.time.DateTime;

import cineast.core.db.DBSelector;
import cineast.core.util.LogHelper;

public class CleanupThreadTest {
	private static final Logger LOGGER = LogManager.getLogger();
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		DBSelector selector = new DBSelector();
		
		DateTime now = DateTime.now();
		
		DateTime targetTime = DateTime.now();
		targetTime = targetTime.withHourOfDay(2);
		targetTime = targetTime.plusDays(1);
		
		long secondsToSleep = targetTime.toDate().getTime() - now.toDate().getTime();
		System.err.println(targetTime.toDate().getTime()+" - "+now.toDate().getTime());
		System.err.println("Seconds to Sleept until 2 o'clock:" + secondsToSleep);
		try {
			Thread.sleep( secondsToSleep);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	
		while(true){
			LOGGER.debug("resetting indices");
				if(selector.executeFunction("select reset_indices();")){
					LOGGER.debug("indices resetted");
				}else{
					LOGGER.error("indices resetting error");
				}
			try {
				Thread.sleep( 2000/*secondsToSleept * 1000*/);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				LOGGER.error(LogHelper.getStackTrace(e));
			}
			
			System.err.println("now executing something");
		}
		
		
		
	
	

	}

}
