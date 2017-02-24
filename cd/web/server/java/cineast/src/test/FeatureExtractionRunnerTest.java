package test;

import java.io.File;
import java.util.concurrent.TimeUnit;

import cineast.core.run.FeatureExtractionRunner;

public class FeatureExtractionRunnerTest {

	public static void main(String[] args) {
		
		FeatureExtractionRunner runner = new FeatureExtractionRunner("/var/www/html/filigrana/web/client/img/photos/11_11_2014", "/var/www/html/filigrana/web/client/img/test/testimagetmp.png", true,false, 129);
		
		long globalStart = System.currentTimeMillis();
		System.err.println("beginning extraction");
		int count = runner.extract();
		System.err.println(count +" Images Processed");
			
	}
	
	private static String formatTime(long ms){
		return String.format("%02d:%02d:%02d", TimeUnit.MILLISECONDS.toHours(ms),
	            TimeUnit.MILLISECONDS.toMinutes(ms) - TimeUnit.HOURS.toMinutes(TimeUnit.MILLISECONDS.toHours(ms)),
	            TimeUnit.MILLISECONDS.toSeconds(ms) - TimeUnit.MINUTES.toSeconds(TimeUnit.MILLISECONDS.toMinutes(ms)));
	}

}
