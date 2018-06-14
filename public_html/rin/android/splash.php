<pre>
package com.example.startingpoint;

import android.app.Activity;
import android.content.Intent;
import android.media.MediaPlayer;
import android.os.Bundle;

public class Splash extends Activity{

	MediaPlayer mp;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO 自動生成されたメソッド・スタブ
		super.onCreate(savedInstanceState);
		setContentView(R.layout.splash);

		mp = MediaPlayer.create(Splash.this, R.raw.flight_in_the_blue_sky);
		mp.start();

		Thread timer = new Thread(){
			public void run(){
				try{
					sleep(3000);
				} catch(InterruptedException e){
					e.printStackTrace();
				} finally {
					Intent i = new Intent("com.example.startingpoint.MENU");
					startActivity(i);
				}
			}
		};
		timer.start();
	}

	@Override
	protected void onPause() {
		// TODO 自動生成されたメソッド・スタブ
		super.onPause();
		finish();
		mp.release();
	}
}

</pre>