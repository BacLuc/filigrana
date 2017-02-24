package test;

import gnu.trove.map.hash.TObjectIntHashMap;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Random;
import java.util.Set;

public class RandomPositionPicker {

	public static void main(String[] args) throws IOException {
		
		TObjectIntHashMap<String> videoLengths = new TObjectIntHashMap<>();
		
		File inputFile = new File("videos.tsv");
		BufferedReader reader = new BufferedReader(new FileReader(inputFile));
		String line = null;
		while((line = reader.readLine()) != null){
			String[] split = line.split("\t");
			String[] hms = split[1].split(":");
			int seconds = Integer.parseInt(hms[2]) + Integer.parseInt(hms[1]) * 60;
			videoLengths.put(split[0], seconds);
			System.out.println(split[0] + ": " + seconds);
		}
		
		reader.close();
		reader = new BufferedReader(new InputStreamReader(System.in));

		Random random = new Random();
		
		String[] videos = new String[videoLengths.size()];
		Set<String> videoSet = videoLengths.keySet();
		int i = 0;
		for(String s : videoSet){
			videos[i++] = s;
		}
		
		while(true){
			reader.readLine();
			String video = videos[random.nextInt(videos.length)];
			int length = videoLengths.get(video);
			int position = random.nextInt(length);
			System.out.println(video + "\t" + toMS(position));
		}
	}

	private static String toMS(int seconds){
		int minutes = seconds / 60;
		seconds -= minutes * 60;
		return minutes + ":" + (seconds > 9 ? seconds : "0" + seconds);
	}
	
}
