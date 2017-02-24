package cineast.core.color;

import cineast.core.data.FloatVector;

public class LabContainer extends ReadableLabContainer implements FloatVector{

	public LabContainer(double L, double a, double b) {
		super(L, a, b);
	}
	
	public LabContainer(float L, float a, float b) {
		super(L, a, b);
	}
	
	public LabContainer(ReadableLabContainer lab){
		super(lab.L, lab.a, lab.b);
	}

	public void setL(float L){
		this.L = L;
	}
	
	public void setA(float a){
		this.a = a;
	}
	
	public void setB(float b){
		this.b = b;
	}
	
	@Override
	public void setElement(int num, float val) {
		switch(num){
		case 0:{L = val; break;}
		case 1:{a = val; break;}
		case 2:{b = val; break;}
		}
	}
}
