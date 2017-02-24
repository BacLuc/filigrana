package test;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;

import com.google.common.base.Predicate;
import com.google.common.collect.TreeTraverser;
import com.google.common.io.Files; 

import org.apache.logging.log4j.core.util.FileUtils;

import  java.io.OutputStream;
import java.util.LinkedList;

public class FileSystemTest {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		
		String originpath = "/var/www/html/filigrana/data/Watermarks/1.jpg";
		String targetpath = "/var/www/html/filigrana/web/client/img/photos/testfile.jpg";
		
		try {
			Files.move(new File(originpath), new File(targetpath));
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		/*
		 char[] charArray = new char[200];
		TreeTraverser<File> traverser = Files.fileTreeTraverser();
		File file = new File("/var/www/html/filigrana/data/Watermarks/2.jpg");
		File file2 = new File("/var/www/html/filigrana/data/Watermarks");
		
		
		
		System.out.print("File 1 is :");
		if(file.getName().lastIndexOf(".") > file.getName().lastIndexOf("/")){
			System.out.print("a File \n");
		}else{
			System.out.print("a Directory \n");
		}
		
		System.out.print("File 2 is :");
		if(file2.getName().lastIndexOf(".") > file2.getName().lastIndexOf("/")){
			System.out.print("a File \n");
		}else{
			System.out.print("a Directory \n");
			
			Runtime runtime = Runtime.getRuntime();
			try {
				Process proc = runtime.exec("ls "+file2.getAbsolutePath());
				InputStream output = proc.getInputStream();
				String string = "";
				char out;
				int i;
				int taketext=0;
				LinkedList<String> fileList = new LinkedList<String>();
				while((i = output.read()) != -1){
					 out = (char)i;
					 
					 string += out;
					 if(out == 10){
						 fileList.add(string);
						 string = "";
					 }
					
				}
			
				System.out.println(string);
				
				for(String filesting : fileList){
					System.out.print("File: "+filesting);
				}
				
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		*/
	}

}
