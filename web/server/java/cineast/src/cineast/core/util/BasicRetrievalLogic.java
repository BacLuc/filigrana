package cineast.core.util;

import java.awt.image.BufferedImage;
import java.io.IOException;
import java.util.HashMap;
import java.util.List;

import cineast.api.API;
import cineast.core.data.LongDoublePair;
import cineast.core.data.MultiImageFactory;
import cineast.core.data.QueryContainer;
import cineast.core.db.DBSelector;
import cineast.core.db.ShotDescriptor;
import cineast.core.db.ShotLookup;
import cineast.core.features.retriever.Retriever;
import cineast.core.features.retriever.RetrieverInitializer;
import cineast.core.runtime.QueryDispatcher;

public class BasicRetrievalLogic {

	public static void main(String[] args) throws IOException {

		// File input = new File("query3.png");
		// BufferedImage bimg = ImageIO.read(input);
		//
		// List<LongDoublePair> result = retrieve(bimg);

		List<LongDoublePair> result = retrieve(100);

		ShotLookup sl = new ShotLookup();

		System.out.println("\nRESULTS:\n");
		for (LongDoublePair p : result) {
			long shotId = p.key;
			ShotDescriptor descriptor = sl.lookUpShot(shotId);
			System.out
					.println("("
							+ p.value
							+ "): "
							+ descriptor.getName()
							+ " @ "
							+ frameToTime(descriptor.getStartFrame(),
									descriptor.getFPS())
							+ " to "
							+ frameToTime(descriptor.getEndFrame(),
									descriptor.getFPS()) + " shotid: " + shotId);
		}

		sl.close();

		System.out.println("done");

	}

	static List<LongDoublePair> retrieve(BufferedImage bimg) {
		return retrieve(new QueryContainer(
				MultiImageFactory.newInMemoryMultiImage(bimg)));
	}

	public static List<LongDoublePair> retrieve(
			HashMap<Retriever, Double> featureWeights, QueryContainer query) {

		long startTime = System.currentTimeMillis();

		QueryDispatcher dispatcher = new QueryDispatcher(featureWeights,
				new RetrieverInitializer() {

					@Override
					public void initialize(Retriever r) {
						r.init(new DBSelector());

					}
				}, query);

		List<LongDoublePair> result = dispatcher.call();
		System.out.println("\nRetrievaltime: "
				+ (System.currentTimeMillis() - startTime) + "ms\n");

		return result;

	}

	public static List<LongDoublePair> retrieve(QueryContainer query) {
		return retrieve(API.getRetrievers(), query);
	}

	static List<LongDoublePair> retrieve(long id) {

		long startTime = System.currentTimeMillis();

		HashMap<Retriever, Double> featureWeights = API.getRetrievers();

		QueryDispatcher dispatcher = new QueryDispatcher(featureWeights,
				new RetrieverInitializer() {

					@Override
					public void initialize(Retriever r) {
						r.init(new DBSelector());

					}
				}, id);

		List<LongDoublePair> result = dispatcher.call();

		System.out.println("\nRetrievaltime: "
				+ (System.currentTimeMillis() - startTime) + "ms\n");

		return result;

	}

	static String frameToTime(int framenum, float fps) {
		int min = (int) (framenum / fps / 60);
		int sec = (int) (framenum / fps - min * 60);

		return min + ":" + (sec < 10 ? "0" + sec : sec);
	}

}
