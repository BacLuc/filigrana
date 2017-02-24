package cineast.core.color;

public class ReadableRGBContainer extends AbstractColorContainer<ReadableRGBContainer> implements Cloneable{

	protected int r, g, b;
	
	public ReadableRGBContainer(int r, int g, int b){
		if(r > 255 || r < 0){
			throw new IllegalArgumentException(r + "is outside of allowed range for red");
		}
		if(g > 255 || g < 0){
			throw new IllegalArgumentException(g + "is outside of allowed range for green");
		}
		if(b > 255 || b < 0){
			throw new IllegalArgumentException(b + "is outside of allowed range for blue");
		}
		
		this.r = r;
		this.g = g;
		this.b = b;
	}
	
	public ReadableRGBContainer(float r, float g, float b){
		this(	Math.round(255f * r), 
				Math.round(255f * g), 
				Math.round(255f * b));
	}
	
	public ReadableRGBContainer(double r, double g, double b){
		this((float)r, (float)g, (float)b);
	}
	
	public ReadableRGBContainer(int color){
		this((color >> 16 & 0xFF), (color >> 8 & 0xFF), (color & 0xFF));
	}
	
	public int toIntColor(){
		return (b & 0xFF) | ((g & 0xFF) << 8) | ((r & 0xFF) << 16) | (255 << 24);
	}
	
	@Override
	public String toString() {
		return "RGBContainer(" + r + ", " + g + ", " + b + ")";
	}
	
	public float getLuminance(){
		return  0.2126f * r + 0.7152f * g + 0.0722f * b;
	}
	
	public static int getRed(int color){
		return (color >> 16 & 0xFF);
	}
	
	public static int getGreen(int color){
		return (color >> 8 & 0xFF);
	}
	
	public static int getBlue(int color){
		return (color & 0xFF);
	}
	
	public static float getLuminance(int r, int g, int b){
		return  0.2126f * r + 0.7152f * g + 0.0722f * b;
	}
	
	public static float getLuminance(int color){
		return getLuminance(getRed(color), getGreen(color), getBlue(color));
	}
	
	public static int toIntColor(int r, int g, int b){
		return (b & 0xFF) | ((g & 0xFF) << 8) | ((r & 0xFF) << 16) | (255 << 24);
	}

	@Override
	public float getElement(int num) {
		switch (num) {
		case 0: return r / 255f;
		case 1: return g / 255f;
		case 2: return b / 255f;
		default: throw new IndexOutOfBoundsException(num + ">= 3");
		}
	}
	
	@Override
	public String toFeatureString() {
		return "<" + r + ", " + g + ", " + b + ">";
	}
}
