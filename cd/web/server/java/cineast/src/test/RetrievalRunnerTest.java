package test;

import java.io.File;
import java.util.concurrent.TimeUnit;

import cineast.core.run.FeatureExtractionRunner;
import cineast.core.run.RetrievalRunner;

public class RetrievalRunnerTest {

	public static void main(String[] args) {
		
		RetrievalRunner runner = new RetrievalRunner("/var/www/html/filigrana/web/client/img/test/testsketch.jpg", false, false);
		
		long globalStart = System.currentTimeMillis();
		System.err.println("beginning extraction");
		String count = runner.retrieve();
		System.err.println("result is: "+count);
			
	}
	
	private static String formatTime(long ms){
		return String.format("%02d:%02d:%02d", TimeUnit.MILLISECONDS.toHours(ms),
	            TimeUnit.MILLISECONDS.toMinutes(ms) - TimeUnit.HOURS.toMinutes(TimeUnit.MILLISECONDS.toHours(ms)),
	            TimeUnit.MILLISECONDS.toSeconds(ms) - TimeUnit.MINUTES.toSeconds(TimeUnit.MILLISECONDS.toMinutes(ms)));
	}

}
