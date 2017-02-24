package test;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;

public class OSVCtoLaTeX {

	public static void main(String[] args) throws IOException {
		BufferedReader reader = new BufferedReader(new FileReader(new File("open short video collection.csv")));

		//\item[Video name] \hfill \\ Author \quad licence \quad \\url{url}
		
		String line;
		while((line = reader.readLine()) != null){
			String[] split = line.split(",");
			//System.out.println("\\item[" + split[0] + "] \\hfill \\\\ " + split[1] + " \\quad " + split[2] + " \\quad \\url{" + split[3] + "}");
			System.out.println("<tr><td><a href=\"" + split[3] + "\">" + split[0] + "</a></td><td>" + split[1] + "</td><td>" + split[2] + "</td></tr>");
		}
		reader.close();
	}

}
