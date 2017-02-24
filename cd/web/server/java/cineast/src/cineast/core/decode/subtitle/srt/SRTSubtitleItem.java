package cineast.core.decode.subtitle.srt;

import cineast.core.decode.subtitle.SubTitle;
import cineast.core.decode.subtitle.SubtitleItem;

public class SRTSubtitleItem implements SubtitleItem {

	int id;
	long start, end;
	String text;
	SRTSubTitle st;
	
	public SRTSubtitleItem(int id, long start, long end, String text, SRTSubTitle st){
		this.id = id;
		this.start = start;
		this.end = end;
		this.text = text;
		this.st = st;
	}

	@Override
	public String toString() {
		return "id: " + id + "\n" + start + " ---> " + end + "\n" + text;
	}
	
	/* (non-Javadoc)
	 * @see subsync.SubItem#getLength()
	 */
	@Override
	public int getLength(){
		return (int) (end - start);
	}
	
	/* (non-Javadoc)
	 * @see subsync.SubItem#getRawText()
	 */
	@Override
	public String getRawText(){
		return this.text;
	}
	
	/* (non-Javadoc)
	 * @see subsync.SubItem#getText()
	 */
	@Override
	public String getText(){
		return this.text.replaceAll("<[^>]*>", "");
	}

	@Override
	public int getStartFrame() {
		return Math.round(this.start * this.st.getFrameRate() / 1000);
	}

	@Override
	public int getEndFrame() {
		return Math.round(this.end * this.st.getFrameRate() / 1000);
	}

	@Override
	public SubTitle getSubTitle() {
		return this.st;
	}
	
}
