package test;

import java.io.File;
import java.util.concurrent.TimeUnit;

import cineast.core.run.FeatureExtractionRunner;

public class FeatureExtractionRunnerTest {

	public static void main(String[] args) {
            
                String targetpath = "/var/www/html/filigrana/web/client/img/photos/28_03_2017";
                String sourcepath = "/var/www/html/filigrana/web/data/iphclasses";
                boolean watermark = false;
                if(args.length >0){
                    targetpath = args[0];
                }
                if(args.length > 1){
                    sourcepath = args[1];
                }
                String s = new String(new char[]{'1'});
                if(args.length > 2){
                    if(args[2].equals(s)){
                        watermark = true;
                    }
                }
		
		FeatureExtractionRunner runner = new FeatureExtractionRunner(targetpath, sourcepath, watermark,false);
		
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

