package cineast.core.segmenter;

import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;
import java.util.concurrent.atomic.AtomicInteger;

import cineast.core.data.Frame;
import cineast.core.data.Histogram;
import cineast.core.data.Shot;
import cineast.core.data.providers.ShotProvider;
import cineast.core.db.LongReturning;
import cineast.core.db.PersistencyWriter;
import cineast.core.db.PersistentTuple;
import cineast.core.db.ShotDescriptor;
import cineast.core.decode.subtitle.SubTitle;
import cineast.core.decode.subtitle.SubtitleItem;
import cineast.core.decode.video.VideoDecoder;

public class ShotSegmenter implements ShotProvider{
	
	private static final double THRESHOLD = 0.05;
	private static final int PRE_QUEUE_LEN = 10;
	private static final int MAX_SHOT_LENGTH = 720;

	private VideoDecoder vdecoder;
	private final int movieId;
	private LinkedList<Frame> frameQueue = new LinkedList<>();
	private LinkedList<DoublePair<Frame>> preShotQueue = new LinkedList<>();
	private ArrayList<SubTitle> subtitles = new ArrayList<SubTitle>();
	private PersistencyWriter pwriter;
	private List<ShotDescriptor> knownShotBoundaries;
	
	public ShotSegmenter(VideoDecoder vdecoder, int movieId, PersistencyWriter pwriter, List<ShotDescriptor> knownShotBoundaries){
		this.vdecoder = vdecoder;
		this.movieId = movieId;
		this.pwriter = pwriter;
		this.pwriter.open("cineast.shots");
		this.knownShotBoundaries = ((knownShotBoundaries == null) ? new LinkedList<ShotDescriptor>() : knownShotBoundaries);
	}
	
	public void addSubTitle(SubTitle st) {
		this.subtitles.add(st);
	}
	
	private boolean queueFrames(){
		return queueFrames(20);
	}
	
	private boolean queueFrames(int number){
		Frame f;
		for(int i = 0; i < number; ++i){
			f = this.vdecoder.getFrame();
			if(f == null){ //no more frames
				return false;
			}else{
				this.frameQueue.offer(f);
			}
		}
		return true;
	}
	
	
	
	public Shot getNextShot(){
		if(this.frameQueue.isEmpty()){
			queueFrames();
		}
		
		Shot _return = null;
		
		if (!preShotQueue.isEmpty()){
			_return = new Shot(this.movieId, this.vdecoder.getTotalFrameCount());
			while (!preShotQueue.isEmpty()) {
				_return.addFrame(preShotQueue.removeFirst().first);
			}
		}
		if(this.frameQueue.isEmpty()){
			return finishShot(_return); //no more shots to segment
		}
		
		if(_return == null){
			_return = new Shot(this.movieId, this.vdecoder.getTotalFrameCount());
		}
		
		
		Frame frame = this.frameQueue.poll();
		
		ShotDescriptor bounds = this.knownShotBoundaries.size() > 0 ? this.knownShotBoundaries.remove(0) : null;
		
		if (bounds != null && frame.getId() >= bounds.getStartFrame() && frame.getId() <= bounds.getEndFrame()){
			
			_return.addFrame(frame);
			queueFrames(bounds.getEndFrame() - bounds.getStartFrame());
			do{
				frame = this.frameQueue.poll();
				if(frame != null){
					_return.addFrame(frame);
				}else{
					break;
				}
				
			}while(frame.getId() < bounds.getEndFrame());
			
			_return.setShotId(bounds.getShotId());
			addSubtitleItems(_return);
			
			return _return;
			
		}else{
			Histogram hPrev, h = getHistogram(frame);
			_return.addFrame(frame);
			while (true) {
				if ((frame = this.frameQueue.poll()) == null) {
					queueFrames();
					if ((frame = this.frameQueue.poll()) == null) {
						return finishShot(_return);
					}
				}
				hPrev = h;
				h = getHistogram(frame);
				double distance = hPrev.getDistance(h);

				preShotQueue.offer(new DoublePair<Frame>(frame, distance));

				if (preShotQueue.size() > PRE_QUEUE_LEN) {
					double max = 0;
					int index = -1, i = 0;
					for (DoublePair<Frame> pair : preShotQueue) {
						if (pair.second > max) {
							index = i;
							max = pair.second;
						}
						i++;
					}
					if (max <= THRESHOLD && _return.getNumberOfFrames() < MAX_SHOT_LENGTH) { //no cut
						for (DoublePair<Frame> pair : preShotQueue) {
							_return.addFrame(pair.first);
						}
						preShotQueue.clear();
					} else {
						for (i = 0; i < index; ++i) {
							_return.addFrame(preShotQueue.removeFirst().first);
						}
						break;
					}
				}
			}
			return finishShot(_return);
		}
	}
	
	private static Histogram getHistogram(Frame f){
		return FuzzyColorHistogramCalculator.getSubdividedHistogramNormalized(f.getImage().getThumbnailImage(), 3);
	}

	private static AtomicInteger idCounter = new AtomicInteger(0);
	
	private int shotNumber = 0;
	private Shot finishShot(Shot shot){
		
		if(shot == null){
			return null;
		}
		
		shot.setShotId(idCounter.incrementAndGet());
		addSubtitleItems(shot);
		
		PersistentTuple tuple = this.pwriter.makeTuple(shotNumber++, movieId, shot.getStart(), shot.getEnd());
		this.pwriter.write(tuple);
		shot.setShotId(((LongReturning)tuple).getReturnValue());

		
		return shot;
	}
	
	private void addSubtitleItems(Shot shot){
		int start = shot.getStart();
		int end = shot.getEnd();
		for(SubTitle st : this.subtitles){
			for(int i = 1; i <= st.getNumerOfItems(); ++i){
				SubtitleItem si = st.getItem(i);
				if(si == null || start > si.getEndFrame() || end < si.getStartFrame()){
					continue;
				}
				shot.addSubtitleItem(si);
			}
		}
	}
	
}

class DoublePair<K>{
	K first;
	double second;
	
	DoublePair(K first, double second){
		this.first = first;
		this.second = second;
	}
}