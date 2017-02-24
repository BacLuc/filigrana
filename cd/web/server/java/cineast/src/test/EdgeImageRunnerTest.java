package test;

import java.io.File;
import java.util.concurrent.TimeUnit;

import cineast.core.run.EdgeImageRunner;
import cineast.core.run.FeatureExtractionRunner;

public class EdgeImageRunnerTest {

	public static void main(String[] args) {
		
		EdgeImageRunner runner = new EdgeImageRunner("/var/www/html/filigrana/web/client/img/test/testpictures/testiph.jpg");
		
		long globalStart = System.currentTimeMillis();
		System.err.println("beginning extraction");
		String result = runner.makeEdgeImg();
		System.err.println(result +" Images Processed");
			
	}
	
	private static String formatTime(long ms){
		return String.format("%02d:%02d:%02d", TimeUnit.MILLISECONDS.toHours(ms),
	            TimeUnit.MILLISECONDS.toMinutes(ms) - TimeUnit.HOURS.toMinutes(TimeUnit.MILLISECONDS.toHours(ms)),
	            TimeUnit.MILLISECONDS.toSeconds(ms) - TimeUnit.MINUTES.toSeconds(TimeUnit.MILLISECONDS.toMinutes(ms)));
	}

}
