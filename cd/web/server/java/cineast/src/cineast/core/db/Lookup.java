package cineast.core.db;

import java.util.List;

import cineast.core.db.ShotDescriptor;

public interface Lookup {
	
		
		
		
		public void close();
		
		public ShotDescriptor lookUpShot(long shotId);
		
		
		public int lookUpVideoid(String name);
		
		public List<ShotDescriptor> lookUpVideo(int videoId);
		
		
		public void finalize() throws Throwable;

}
