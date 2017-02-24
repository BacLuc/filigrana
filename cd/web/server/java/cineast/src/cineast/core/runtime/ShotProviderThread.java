package cineast.core.runtime;

import java.util.concurrent.LinkedBlockingQueue;

import cineast.core.data.FrameContainer;
import cineast.core.data.providers.ShotProvider;

class ShotProviderThread extends Thread {

	private LinkedBlockingQueue<FrameContainer> shotQueue;
	private ShotProvider provider;
	
	ShotProviderThread(LinkedBlockingQueue<FrameContainer> shotQueue, ShotProvider provider) {
		super("ShotProviderThread");
		this.provider = provider;
		this.shotQueue = shotQueue;
	}
	
	@Override
	public void run() {
		FrameContainer shot;
		while((shot = provider.getNextShot()) != null && !this.isInterrupted()){
			try {
				this.shotQueue.put(shot);
			} catch (InterruptedException e) {
				this.interrupt();
			}
		}
	}

	
	
}
