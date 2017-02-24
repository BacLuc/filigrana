package test;

import java.awt.Color;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

import javax.imageio.ImageIO;

public class PrintImage {

	public static void main(String[] args) throws IOException {
		BufferedImage img = ImageIO.read(new File("scales.png"));
		int width = img.getWidth(), height = img.getHeight();
		for(int y = 0; y < height; ++y){
			for(int x = 0; x < width; ++x){
				Color color = new Color(img.getRGB(x, y));
				int r = color.getRed(), g = color.getGreen(), b = color.getBlue();
				String out = "#";
				if(r < 16){
					out += "0";
				}
				out += Integer.toHexString(r).toUpperCase();
				
				if(g < 16){
					out += "0";
				}
				out += Integer.toHexString(g).toUpperCase();
				
				if(b < 16){
					out += "0";
				}
				out += Integer.toHexString(b).toUpperCase();
				
				System.out.print(out);
				System.out.print(", ");
			}
			System.out.println();
		}

	}

}
