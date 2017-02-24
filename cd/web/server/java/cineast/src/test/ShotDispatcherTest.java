package test;

import java.io.File;
import java.io.FileFilter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import cineast.core.db.ADAMTuple;
import cineast.core.db.ADAMWriter;
import cineast.core.db.ReturningADAMTuple;
import cineast.core.db.ShotLookup;
import cineast.core.db.ShotDescriptor;
import cineast.core.decode.subtitle.SubTitle;
import cineast.core.decode.subtitle.srt.SRTSubTitle;
import cineast.core.decode.video.JCodecVideoDecoder;
import cineast.core.decode.video.VideoDecoder;

import cineast.core.features.exporter.MotionFrameExporter;
import cineast.core.features.exporter.ShotDescriptorExporter;
import cineast.core.features.extractor.Extractor;
import cineast.core.features.extractor.ExtractorInitializer;
import cineast.core.runtime.ShotDispatcher;
import cineast.core.segmenter.ShotSegmenter;

public class ShotDispatcherTest {

	private static final Logger LOGGER = LogManager.getLogger();
	private static File folder = new File("F:/osvc/collection");
	
	private static final String database = "127.0.0.1:5434/cineast", user = "cineast", pass = "ilikemovies";
	
	/**
	 * @param args
	 * @throws IOException 
	 */
	public static void main(String[] args) throws IOException {
		
		String name = "Big Buck Bunny";
		String path = name + "/" + "Big_Buck_Bunny.mp4";
		
		
		ADAMWriter writer = new ADAMWriter(database, user, pass, "id"){

			@Override
			public int getParameterCount() {
				return 6;
			}

			@Override
			public String[] getParameterNames() {
				return new String[]{"name", "path", "width", "height", "frames", "seconds"};
			}
			
		};
		
		VideoDecoder vd = new JCodecVideoDecoder(new File(folder, path));
		
		
		LOGGER.debug("Total frames: {}", vd.getTotalFrameCount());
		LOGGER.debug("frames per second: {}", vd.getFPS());
		
		writer.open("cineast.videos");
		
		List<ShotDescriptor> knownShots = null;
		int id;
		
		if(writer.check("select * from cineast.videos where name = \'" + ADAMTuple.escape(name) + "\'")){
			System.err.println(name + " allready in database");
			ShotLookup lookup = new ShotLookup(database, user, pass);
			id = lookup.lookUpVideoid(ADAMTuple.escape(name));
			knownShots = lookup.lookUpVideo(id);
//			for(ShotDescriptor sd : knownShots){
//				System.out.println(sd);
//			}
			lookup.close();
		}else{
			ReturningADAMTuple tuple = (ReturningADAMTuple) writer.makeTuple(name, path, vd.getWidth(), vd.getHeight(), vd.getTotalFrameCount(), vd.getTotalFrameCount() / vd.getFPS());
			writer.write(tuple);
			
			id = (int) tuple.getReturnValue();
		}
		
		
		ShotSegmenter segmenter = new ShotSegmenter(vd, id, new ADAMWriter(database, user, pass, "id") {
			
			@Override
			public String[] getParameterNames() {
				return new String[]{"number", "video", "startFrame", "endFrame"};
			}
			
			@Override
			public int getParameterCount() {
				return 4;
			}
		}, knownShots);
		

		//search subtitles
		File inputfolder = new File(folder, name);
		File[] subtitleFiles = inputfolder.listFiles(new FileFilter() {
			
			@Override
			public boolean accept(File pathname) {
				return pathname.getAbsolutePath().toLowerCase().endsWith(".srt");
			}
		});
		for(File f : subtitleFiles){
			SubTitle st = new SRTSubTitle(f, (float) vd.getFPS());
			segmenter.addSubTitle(st);
			LOGGER.info("added Subtitle " + f.getAbsolutePath() + " to segmenter");
		}
		
		ArrayList<Extractor> featureList = new ArrayList<>();
//		featureList.add(new AverageColor());
//		featureList.add(new Contrast());
//		featureList.add(new DominantColors());
//		featureList.add(new MedianColor());
//		featureList.add(new SaturationAndChroma());
//		featureList.add(new AverageFuzzyHist());
//		featureList.add(new MedianFuzzyHist());
//		featureList.add(new AverageColorARP44());
//		featureList.add(new MedianColorARP44());
//		featureList.add(new SubDivAverageFuzzyColor());
//		featureList.add(new SubDivMedianFuzzyColor());
//		featureList.add(new AverageColorGrid8());
//		featureList.add(new ChromaGrid8());
//		featureList.add(new SaturationGrid8());
//		featureList.add(new AverageColorCLD());
//		featureList.add(new CLD());
//		featureList.add(new HueValueVarianceGrid8());
//		featureList.add(new MedianColorGrid8());
//		featureList.add(new EdgeARP88());
//		featureList.add(new EdgeGrid16());
//		featureList.add(new ShotThumbNails());
//		featureList.add(new EdgeARP88Full());
//		featureList.add(new EdgeGrid16Full());
//		featureList.add(new EHD());
//		featureList.add(new SubtitleFulltextSearch());
//		featureList.add(new AverageColorRaster());
//		featureList.add(new MotionHistogram());
//		featureList.add(new SubDivMotionHistogram2());
//		featureList.add(new SubDivMotionHistogram3());
//		featureList.add(new SubDivMotionHistogram4());
//		featureList.add(new Edgels());
//		featureList.add(new EdgelsFull());
		
//		featureList.add(new MotionFrameExporter());
//		featureList.add(new ShotDescriptorExporter());
		
		ExtractorInitializer initializer = new ExtractorInitializer() {
			
			@Override
			public void initialize(Extractor e) {
				e.init(new ADAMWriter(database, user, pass){

					@Override
					public int getParameterCount() {
						return 0;
					}

					@Override
					public String[] getParameterNames() {
						return null;
					}
					
				});				
			}
		};
		
		ShotDispatcher dispatcher = new ShotDispatcher(featureList, initializer, segmenter);
		
		dispatcher.run();		
		
		System.out.println("done");

	}
}

//class StreamPersistencyHandler implements PersistencyWriter<StringTuple>{
//
//	private PrintWriter writer;
//	
//	@Override
//	public boolean open(String param) {
//		try {
//			this.writer = new PrintWriter(new File(param + ".featureData"));
//			return true;
//		} catch (IOException e) {
//			return false;
//		}
//	}
//
//	@Override
//	public void setPrefix(String... prefixes) {
//		// TODO Auto-generated method stub
//		
//	}
//
//	@Override
//	public StringTuple makeTuple(Object... objects) {
//		StringTuple tuple = new StringTuple(this);
//		for(Object o : objects){
//			tuple.addElement(o);
//		}
//		return tuple;
//			
//	}
//
//	@Override
//	public void write(StringTuple tuple) {
//		this.writer.println(tuple.getPersistentRepresentation());
//		this.writer.flush();
//	}
//
//
//	@Override
//	public boolean close() {
//		this.writer.flush();
//		this.writer.close();
//		return true;
//	}
//	
//}

//class StringTuple extends PersistentTuple<String>{
//
//	protected StringTuple(PersistencyWriter<StringTuple> phandler) {
//		super(phandler);
//	}
//
//	@Override
//	public String getPersistentRepresentation() {
//		StringBuffer sb = new StringBuffer();
//		for(Object o : elements){
//			if(o == null){
//				sb.append("null, ");
//			}else{
//				sb.append(o.toString());
//				sb.append(", ");
//			}
//		}
//		return sb.toString();
//	}
//	
//}