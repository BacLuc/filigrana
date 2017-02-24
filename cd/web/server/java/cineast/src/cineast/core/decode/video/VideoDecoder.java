package cineast.core.decode.video;

import cineast.core.data.Frame;

public interface VideoDecoder {

	void seekToFrame(int frameNumber);

	int getFrameNumber();

	Frame getFrame();
	
	int getTotalFrameCount();
	
	double getFPS();

	void close();
	
	int getWidth();
	
	int getHeight();

}