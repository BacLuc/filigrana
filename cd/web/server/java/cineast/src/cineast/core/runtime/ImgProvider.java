package cineast.core.runtime;

import cineast.core.data.Frame;
import cineast.core.data.FrameContainer;
import cineast.core.data.MultiImage;
import cineast.core.data.SingleFrameContainer;
import cineast.core.data.providers.ShotProvider;

public class ImgProvider implements ShotProvider {
	private FrameContainer framecontainer;
	
	public ImgProvider( int id, MultiImage img){

		Frame frame = new Frame(id,img);
		this.framecontainer = new SingleFrameContainer(frame, id);
	}
	
	@Override
	public FrameContainer getNextShot() {
		// TODO Auto-generated method stub
		FrameContainer tmp = this.framecontainer;
		this.framecontainer = null;
		return tmp;
	}

}
