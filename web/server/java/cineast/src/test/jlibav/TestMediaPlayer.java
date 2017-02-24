package test.jlibav;

/*
 * Copyright (C) 2012 Ondrej Perutka
 *
 * This program is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public 
 * License as published by the Free Software Foundation, either 
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with this library. If not, see 
 * <http://www.gnu.org/licenses/>.
 */

import java.util.logging.Level;
import java.util.logging.Logger;

import org.libav.DefaultMediaPlayer;
import org.libav.IDecoder;
import org.libav.IMediaReader;
import org.libav.LibavException;
import org.libav.video.VideoFrameDecoder;

public class TestMediaPlayer{

    private IMediaReader mr;
    
    private IDecoder vDecoder;

    
    public TestMediaPlayer(IMediaReader mr, int videoStreamIndex) throws LibavException {
        this.mr = mr;
        this.vDecoder = new VideoFrameDecoder(mr.getVideoStream(videoStreamIndex));
        
        mr.addVideoPacketConsumer(videoStreamIndex, this.vDecoder);
        mr.setVideoStreamBufferingEnabled(videoStreamIndex, true);

    }

    public IDecoder getVideoStreamDecoder(int videoStreamIndex) throws LibavException {

        return vDecoder;
    }
    

    public synchronized void play() throws LibavException {


        
        int streamIndex = 0;
        boolean stop = false;
        
        while (!stop) {
            try {
                if (!mr.readNextPacket(streamIndex))
                    stop = true;
            } catch (LibavException ex) {
                Logger.getLogger(DefaultMediaPlayer.class.getName()).log(Level.SEVERE, "error while playing media", ex);
            }
        }
        
       

    }

    public synchronized void close() throws LibavException {
       

            if (vDecoder != null)
            	vDecoder.close();

        mr.close();
    }
    

    
   
}
