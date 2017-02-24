package filigrana;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;

import cineast.core.util.LogHelper;

import com.google.common.base.Predicate;
import com.google.common.collect.TreeTraverser;
import com.google.common.io.Files; 

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.core.util.FileUtils;

import  java.io.OutputStream;
import java.util.LinkedList;

public class FileSysHandler {
	private static final org.apache.logging.log4j.Logger LOGGER = LogManager.getLogger();
	public static Boolean isDirectory(File file){
		if(file.getName().lastIndexOf(".") > file.getName().lastIndexOf("/")){
			return false;
		}else{
			return true;
		}
	}
	
	public static LinkedList<String> getFilesOfDirectory(File file){
		if(FileSysHandler.isDirectory(file)){
			Runtime runtime = Runtime.getRuntime();
			try {
				Process proc = runtime.exec("ls "+file.getAbsolutePath());
				InputStream output = proc.getInputStream();
				String string = "";
				char out;
				int i;
				LinkedList<String> fileList = new LinkedList<String>();
				while((i = output.read()) != -1){
					 out = (char)i;
					 
					 
					 if(out == 10 || out == 13){
						 fileList.add(file.getAbsoluteFile()+"/"+string);
						 string = "";
					 }else{
						 string += out;
					 }
					
				}
			
				return fileList;
				
			} catch (IOException e) {
				// TODO Auto-generated catch block
				LOGGER.debug(LogHelper.getStackTrace(e));
				
			}
		}
		return new LinkedList<String>();
	}

}
