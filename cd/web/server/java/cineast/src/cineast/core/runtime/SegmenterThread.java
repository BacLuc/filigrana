package cineast.core.runtime;

import java.util.concurrent.LinkedBlockingQueue;

import cineast.core.data.Shot;
import cineast.core.segmenter.ShotSegmenter;

class SegmenterThread extends Thread {

	private LinkedBlockingQueue<Shot> shotQueue;
	private ShotSegmenter segmenter;
	
	SegmenterThread(LinkedBlockingQueue<Shot> shotQueue, ShotSegmenter segmenter) {
		super("SegmenterThread");
		this.segmenter = segmenter;
		this.shotQueue = shotQueue;
	}
	
	@Override
	public void run() {
		Shot shot;
		while((shot = segmenter.getNextShot()) != null && !this.isInterrupted()){
			try {
				this.shotQueue.put(shot);
			} catch (InterruptedException e) {
				this.interrupt();
			}
		}
	}

	
	
}
