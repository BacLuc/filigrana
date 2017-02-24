
package cineast.api;




import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.io.Reader;
import java.net.Socket;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;

import cineast.core.config.Config;
import cineast.core.run.FeatureExtractionRunner;
import cineast.core.run.RetrievalRunner;
import cineast.core.util.LogHelper;

import com.eclipsesource.json.JsonObject;

public class JSONAPIThread extends Thread {

	private Socket socket = null;
	private Reader reader;
	private PrintStream printer;

	private static Logger LOGGER = LogManager.getLogger();


	public JSONAPIThread(Socket socket) throws IOException {
		this.reader = new InputStreamReader(socket.getInputStream());
		this.printer = new PrintStream(socket.getOutputStream());
		this.socket = socket;
	}

	@Override
	public void run() {
		
		
		
		long starttime = System.currentTimeMillis();
		StringBuilder sb = new StringBuilder();
		int i;
		try {
			while ((i = this.reader.read()) != ';') {
				char c = (char) i;
				sb.append(c);
			}
		} catch (IOException e) {
			LOGGER.error(LogHelper.getStackTrace(e));
		}
		String input = sb.toString();
		Object  value=JSONValue.parse(input);

		JSONObject json = (JSONObject)value;
		LOGGER.debug("recieved data:"+ json.toJSONString());
		
		//JsonObject json = JsonObject.readFrom(this.reader);
		
		JsonObject returnObject = new JsonObject();
		returnObject.set("check", "ser");
		String returnString = "";
		//this.printer.print("{a:1}\n");
		try{
			boolean sketch;
			switch(json.get("action").toString()){
				case "extractWM":
					
					if(json.get("sketch").toString().equals("1")){
						sketch = true;
					}else{
						sketch = false;
					}
					if(json.get("id").toString().equals("-1")){
						
						FeatureExtractionRunner runner = new FeatureExtractionRunner(json.get("targetFolder").toString(), json.get("pathToExtract").toString(), true, sketch);
						int count = runner.extract();
						returnObject.set("check", "suc");
						returnObject.set("count", count);
					}else{
						
						int id = Integer.parseInt(json.get("id").toString());
						
						FeatureExtractionRunner runner = new FeatureExtractionRunner(json.get("targetFolder").toString(), json.get("pathToExtract").toString(), true,sketch, id);
						int count = runner.extract();
						returnObject.set("check", "suc");
						returnObject.set("count", count);
						
					}
					returnString = returnObject.toString();
					break;
					
				case "extractIPH":
					
					if(json.get("sketch").toString().equals("1")){
						sketch = true;
					}else{
						sketch = false;
					}
					if(json.get("id").toString().equals("-1")){
						
						FeatureExtractionRunner runner = new FeatureExtractionRunner(json.get("targetFolder").toString(), json.get("pathToExtract").toString(), false,sketch );
						int count = runner.extract();
						returnObject.set("check", "suc");
						returnObject.set("count", count);
					}else{
						int id = Integer.parseInt(json.get("id").toString());	
						FeatureExtractionRunner runner = new FeatureExtractionRunner(json.get("targetFolder").toString(), json.get("pathToExtract").toString(),false,sketch, id);
						int count = runner.extract();
						returnObject.set("check", "suc");
						returnObject.set("count", count);
						
					}
					returnString = returnObject.toString();
					break;
					
					
				case "retrieveWM":
					RetrievalRunner runner = new RetrievalRunner(json.get("path").toString(), true, Config.getWithSketch());
					String result = "";
					if(json.get("picid").equals("")){
						 result = runner.retrieve();
					}else{
						long picid = Long.parseLong(json.get("picid").toString());
						 result = runner.retrieve(picid);
					}
					if(result.equals("")){
						returnString = "{\"check\" : \"ser\"}";
					}else{
						returnString = "{\"check\" : \"suc\", \"result\" : "+result+"}";
					}
					break;
					
				case "retrieveIPH":
					RetrievalRunner runner2 = new RetrievalRunner(json.get("path").toString(), false, Config.getWithSketch());
					String result2 = "";
					if(json.get("picid").equals("")){
						result2 = runner2.retrieve();
					}else{
						long picid = Long.parseLong(json.get("picid").toString());
						result2 = runner2.retrieve(picid);
					}
					if(result2.equals("")){
						returnString = "{\"check\" : \"ser\"}";
					}else{
						returnString = "{\"check\" : \"suc\", \"result\" : "+result2+"}";
					}
					break;
				default:
					returnString = returnObject.toString();
					break;
				
			
			
			}
		}catch(Exception e){
			
			LOGGER.error(LogHelper.getStackTrace(e));
			returnObject = new JsonObject();
			returnObject.set("check", "ser");
			returnString = returnObject.toString();
		}
		LOGGER.debug(returnString);
		this.printer.print(returnString +"\n");
		try {
			this.reader.close();
			this.printer.close();
			if (this.socket != null) {
				this.socket.close();
			}
		} catch (IOException e) {
			LOGGER.warn(LogHelper.getStackTrace(e));
		}
		
		/*
		try {
			select = this.reader.read();
		} catch (IOException e1) {
			LOGGER.error(LogHelper.getStackTrace(e1));
		}
		
		
		if (select == 'i') {
			StringBuilder sb = new StringBuilder();
			int i;
			try {
				while ((i = this.reader.read()) != ';') {
					char c = (char) i;
					sb.append(c);
				}
				String input = sb.toString();
				long id = -1;
				HashMap<Retriever, Double> retrievers;
				if(input.contains("|")){
					String[] split = input.split("\\|");
					id = Long.parseLong(split[0]);
					retrievers = API.getRetrievers(JSONUtils.getWeightsFromJsonString(split[1]));
				}else{
					id = Long.parseLong(input);
					retrievers = API.getRetrievers();
				}
				
				QueryDispatcher dispatcher = new QueryDispatcher(
						retrievers, API.getInitializer(), id);
				List<LongDoublePair> result = dispatcher.call();
				ShotLookup sl = new ShotLookup();
				String output = JSONUtils.resultToJSONString(result, sl);
				sl.close();
				this.printer.print(output);
			} catch (NumberFormatException e) {
				LOGGER.error(LogHelper.getStackTrace(e));
			} catch (IOException e) {
				LOGGER.error(LogHelper.getStackTrace(e));
			}
		} else if (select == 'r') {// relevance feedback
			StringBuilder sb = new StringBuilder();
			int i;
			try {
				while ((i = this.reader.read()) != ';') {
					char c = (char) i;
					sb.append(c);
				}
				String[] input = sb.toString().split("\\|");
				HashMap<Retriever, Double> retrievers = API.getRetrievers(JSONUtils.getWeightsFromJsonString(input[0]));
				String[] positive = input[1].split(",");
				QueryDispatcher dispatcher = new QueryDispatcher(
						retrievers, API.getInitializer(), -1);
				TLongDoubleHashMap map = new TLongDoubleHashMap();
				double wheightSum = positive.length;
				dispatcher.startPool();
				for (String p : positive) {
					if(p.isEmpty()){
						continue;
					}
					try {
						int id = Integer.parseInt(p);
						List<LongDoublePair> list = null;
						try {
							list = dispatcher.retirieve(id);
						} catch (Exception e) {
							list = new LinkedList<LongDoublePair>();
							LOGGER.fatal(LogHelper.getStackTrace(e));
						}
						for (LongDoublePair pair : list) {
							if (Double.isInfinite(pair.value)
									|| Double.isNaN(pair.value)) {
								continue;
							}
							if (!map.contains(pair.key)) {
								map.put(pair.key, pair.value);
							} else {
								map.put(pair.key, map.get(pair.key)
										+ pair.value);
							}
						}
					} catch (NumberFormatException e) {
						LOGGER.error(LogHelper.getStackTrace(e));
					}
				}
				if(input.length > 2){
					String[] negative = input[2].split(",");
					for (String n : negative) {
						if(n.isEmpty()){
							continue;
						}
						try {
							int id = Integer.parseInt(n);
							List<LongDoublePair> list = null;
							try {
								list = dispatcher.retirieve(id);
							} catch (Exception e) {
								list = new LinkedList<LongDoublePair>();
								LOGGER.fatal(LogHelper.getStackTrace(e));
							}
							for (LongDoublePair pair : list) {
								if (Double.isInfinite(pair.value)
										|| Double.isNaN(pair.value)) {
									continue;
								}
								if (!map.contains(pair.key)) {
									map.put(pair.key, -pair.value);
								} else {
									map.put(pair.key, map.get(pair.key)	- pair.value);
								}
							}
						} catch (NumberFormatException e) {
							LOGGER.error(LogHelper.getStackTrace(e));
						}
					}
				}
				
				dispatcher.shutDownPool();

				List<LongDoublePair> _return = new ArrayList<>(map.size());
				long[] keys = map.keys();
				for (long key : keys) {
					double val = map.get(key);
					if (val > 0) {
						_return.add(new LongDoublePair(key, val));
					}
				}

				Collections.sort(_return, new Comparator<LongDoublePair>() {

					@Override
					public int compare(LongDoublePair o1, LongDoublePair o2) {
						return Double.compare(o2.value, o1.value);
					}

				});

				int MAX_RESULTS = Config.maxResults();

				if (_return.size() > MAX_RESULTS) {
					_return = _return.subList(0, MAX_RESULTS - 1);
				}

				for (LongDoublePair p : _return) {
					p.value /= wheightSum;
				}
				
				ShotLookup sl = new ShotLookup();
				String output = JSONUtils.resultToJSONString(_return, sl);
				sl.close();
				this.printer.print(output);

			} catch (IOException e) {
				LOGGER.error(LogHelper.getStackTrace(e));
			}

		} else {
			Pair<QueryContainer, TObjectDoubleHashMap<String>> p = JSONUtils.readQueryFromJSON(this.reader);
			QueryContainer qc = p.first;
			
			QueryDispatcher dispatcher = new QueryDispatcher(
					API.getRetrievers(
							p.second.get("global"),
							p.second.get("local"),
							p.second.get("edge"),
							p.second.get("text"),
							p.second.get("motion"),
							p.second.get("complex")
							), API.getInitializer(), qc);
			List<LongDoublePair> result = dispatcher.call();
			ShotLookup sl = new ShotLookup();
			String output = JSONUtils.resultToJSONString(result, sl);
			sl.close();
			this.printer.print(output);
		}

		this.printer.flush();
		LOGGER.info("retrieval time: {}",
				(System.currentTimeMillis() - starttime));
		try {
			this.reader.close();
			this.printer.close();
			if (this.socket != null) {
				this.socket.close();
			}
		} catch (IOException e) {
			LOGGER.warn(LogHelper.getStackTrace(e));
		}
		*/
	}

}
